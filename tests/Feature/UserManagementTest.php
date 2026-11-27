<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_staff_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Alice Bibliothécaire',
                'email' => 'Alice.Biblio@ENEAM.BJ',
                'role' => 'bibliothecaire',
                'password' => 'motdepasse123',
                'password_confirmation' => 'motdepasse123',
            ])
            ->assertRedirect(route('users.index'));

        $compte = User::where('email', 'alice.biblio@eneam.bj')->firstOrFail();

        $this->assertSame('bibliothecaire', $compte->role);
        $this->assertTrue($compte->actif);
        $this->assertTrue(Hash::check('motdepasse123', $compte->password));
    }

    public function test_admin_cannot_create_a_student_account_from_this_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Étudiant indu',
                'email' => 'etudiant.indesirable@eneam.bj',
                'role' => 'etudiant',
                'password' => 'motdepasse123',
                'password_confirmation' => 'motdepasse123',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'etudiant.indesirable@eneam.bj']);
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('users.toggle', $admin))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->actif);
    }

    public function test_admin_can_deactivate_another_staff_account(): void
    {
        $admin = User::factory()->admin()->create();
        $bibliothecaire = User::factory()->bibliothecaire()->create();

        $this->actingAs($admin)
            ->patch(route('users.toggle', $bibliothecaire))
            ->assertSessionHas('success');

        $this->assertFalse($bibliothecaire->fresh()->actif);

        $this->actingAs($admin)
            ->patch(route('users.toggle', $bibliothecaire))
            ->assertSessionHas('success');

        $this->assertTrue($bibliothecaire->fresh()->actif);
    }

    public function test_only_an_admin_can_manage_staff_accounts(): void
    {
        $bibliothecaire = User::factory()->bibliothecaire()->create();
        $etudiant = User::factory()->create();

        $this->actingAs($bibliothecaire)->get(route('users.index'))->assertForbidden();
        $this->actingAs($etudiant)->get(route('users.create'))->assertForbidden();
    }

    public function test_staff_page_lists_registered_accounts(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Test']);
        $bibliothecaire = User::factory()->bibliothecaire()->create(['name' => 'Biblio Test']);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Comptes du personnel')
            ->assertSee('Admin Test')
            ->assertSee('Biblio Test');
    }
}
