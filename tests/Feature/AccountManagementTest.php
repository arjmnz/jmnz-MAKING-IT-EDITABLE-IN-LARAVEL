<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_create_and_edit_workflow(): void
    {
        $this->get('/customers/create')->assertOk();
        $this->post('/customers', [
            'full_name' => 'Arwind Craig R. Jimenez',
            'email' => 'arjimenez@feualabang.edu.ph',
        ])->assertRedirect('/customers');
        $customer = Customer::firstOrFail();
        $this->assertSame('', $customer->phone);
        $this->get("/customers/{$customer->id}/edit")->assertOk()
            ->assertSee('Arwind Craig R. Jimenez')->assertSee('arjimenez@feualabang.edu.ph');
        $this->put("/customers/{$customer->id}", [
            'full_name' => 'Updated Customer', 'email' => 'updated@example.com', 'phone' => '09123456789',
        ])->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Updated Customer']);
        $this->get('/customers')->assertOk()->assertSee('Updated Customer');
    }

    public function test_customer_validation_preserves_old_input_and_rejects_invalid_email(): void
    {
        $this->from('/customers/create')->post('/customers', [
            'full_name' => 'Retain This Name', 'email' => 'invalid',
        ])->assertRedirect('/customers/create')->assertSessionHasErrors('email')
            ->assertSessionHasInput('full_name', 'Retain This Name');
        $this->get('/customers/create')->assertSee('Retain This Name')->assertSee('role="alert"', false);
        $this->post('/customers', [])->assertSessionHasErrors(['full_name', 'email']);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_user_required_fields_uniqueness_and_editing_own_username(): void
    {
        $this->get('/users/create')->assertOk();
        $this->post('/users', [])->assertSessionHasErrors(['username', 'full_name']);
        $this->post('/users', ['username' => 'arjmnz', 'full_name' => 'Arwind Craig R. Jimenez'])
            ->assertRedirect('/users');
        $user = User::firstOrFail();
        $this->post('/users', ['username' => 'arjmnz', 'full_name' => 'Duplicate'])
            ->assertSessionHasErrors('username');
        $this->get("/users/{$user->id}/edit")->assertOk()->assertSee('arjmnz');
        $this->put("/users/{$user->id}", [
            'username' => 'arjmnz', 'full_name' => 'Arwind Craig R. Jimenez',
            'email' => 'arjimenez@feualabang.edu.ph',
        ])->assertRedirect('/users')->assertSessionHasNoErrors();
        $other = User::create(['username' => 'another', 'full_name' => 'Another User']);
        $this->from("/users/{$other->id}/edit")->put("/users/{$other->id}", [
            'username' => 'arjmnz', 'full_name' => 'Keep This Input',
        ])->assertSessionHasErrors('username')->assertSessionHasInput('full_name', 'Keep This Input');
        $this->get("/users/{$other->id}/edit")->assertSee('Keep This Input');
        $this->assertSame('another', $other->fresh()->username);
    }

    public function test_avatar_is_prepared_stored_displayed_and_replaced(): void
    {
        Storage::fake('public');
        $user = User::create(['username' => 'avataruser', 'full_name' => 'Avatar User']);
        $fields = ['username' => 'avataruser', 'full_name' => 'Avatar User'];
        $this->get('/users')->assertOk()->assertSee('images/avatar-placeholder.png');
        $this->put("/users/{$user->id}", $fields + [
            'avatar' => UploadedFile::fake()->image('portrait.jpg', 600, 400),
        ])->assertRedirect('/users')->assertSessionHasNoErrors();
        $path = $user->fresh()->avatar;
        $this->assertStringStartsWith('avatars/', $path);
        Storage::disk('public')->assertExists($path);
        $image = getimagesize(Storage::disk('public')->path($path));
        $this->assertSame(200, $image[0]);
        $this->assertSame(200, $image[1]);
        $this->assertSame('image/png', $image['mime']);
        $this->get('/users')->assertSee(Storage::disk('public')->url($path));
        $this->put("/users/{$user->id}", $fields)->assertSessionHasNoErrors();
        $this->assertSame($path, $user->fresh()->avatar);
        $this->put("/users/{$user->id}", $fields + [
            'avatar' => UploadedFile::fake()->image('replacement.png', 300, 300),
        ])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_bad_uploads_do_not_modify_the_user_or_store_files(): void
    {
        Storage::fake('public');
        $user = User::create(['username' => 'uploaduser', 'full_name' => 'Original Name']);
        foreach ([
            UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
            UploadedFile::fake()->image('large.jpg')->size(2049),
        ] as $file) {
            $this->put("/users/{$user->id}", [
                'username' => 'uploaduser', 'full_name' => 'Changed Name', 'avatar' => $file,
            ])->assertSessionHasErrors('avatar');
            $this->assertSame('Original Name', $user->fresh()->full_name);
            $this->assertNull($user->fresh()->avatar);
            $this->assertCount(0, Storage::disk('public')->allFiles());
        }
    }

    public function test_missing_records_return_404_and_empty_lists_render(): void
    {
        $this->get('/customers')->assertOk()->assertSee('No customers found.');
        $this->get('/users')->assertOk()->assertSee('No users found.');
        $this->get('/customers/999/edit')->assertNotFound();
        $this->get('/users/999/edit')->assertNotFound();
        $this->put('/customers/999', [])->assertNotFound();
        $this->put('/users/999', [])->assertNotFound();
    }
}
