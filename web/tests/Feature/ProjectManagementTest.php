<?php

namespace Tests\Feature;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_project_and_becomes_its_owner(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/proyectos', [
            'name' => 'TaskFlow 1.0',
            'description' => 'Gestión colaborativa.',
        ]);

        $project = Project::firstOrFail();

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => ProjectRole::Owner->value,
        ]);
    }

    public function test_only_members_can_view_a_project(): void
    {
        [$owner, $project] = $this->createProject();
        $outsider = User::factory()->create();

        $this->actingAs($owner)->get(route('projects.show', $project))->assertOk();
        $this->actingAs($outsider)->get(route('projects.show', $project))->assertForbidden();
    }

    public function test_an_owner_can_add_an_existing_account_as_member(): void
    {
        [$owner, $project] = $this->createProject();
        $member = User::factory()->create(['email' => 'laura@example.test']);

        $this->actingAs($owner)->post(route('project-members.store', $project), [
            'email' => 'laura@example.test',
            'role' => ProjectRole::Member->value,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('project_user', [
            'project_id' => $project->id,
            'user_id' => $member->id,
            'role' => ProjectRole::Member->value,
        ]);
    }

    public function test_the_member_form_is_opened_from_an_owner_project_card(): void
    {
        [$owner, $project] = $this->createProject();
        $member = User::factory()->create();
        $project->members()->attach($member->id, ['role' => ProjectRole::Member->value]);

        $this->actingAs($owner)->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('project-members.create', $project), false);

        $this->actingAs($owner)->get(route('projects.show', $project))
            ->assertOk()
            ->assertDontSee('Añadir miembro');

        $this->actingAs($owner)->get(route('project-members.create', $project))
            ->assertOk()
            ->assertSee('Añadir miembro')
            ->assertSee($project->name);

        $this->actingAs($member)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('project-members.create', $project), false);

        $this->actingAs($member)->get(route('project-members.create', $project))
            ->assertForbidden();
    }

    public function test_a_member_cannot_administer_the_project_or_its_members(): void
    {
        [$owner, $project] = $this->createProject();
        $member = User::factory()->create();
        $other = User::factory()->create();
        $project->members()->attach($member->id, ['role' => ProjectRole::Member->value]);

        $this->actingAs($member)->put(route('projects.update', $project), [
            'name' => 'Cambio no permitido',
        ])->assertForbidden();

        $this->actingAs($member)->post(route('project-members.store', $project), [
            'email' => $other->email,
            'role' => ProjectRole::Member->value,
        ])->assertForbidden();
    }

    public function test_the_last_owner_cannot_be_demoted_or_removed(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->put(route('project-members.update', [$project, $owner]), [
            'role' => ProjectRole::Member->value,
        ])->assertSessionHasErrors('role');

        $this->actingAs($owner)->delete(route('project-members.destroy', [$project, $owner]))
            ->assertSessionHasErrors('member');

        $this->assertTrue($project->isOwner($owner));
    }

    public function test_an_archived_project_is_read_only_and_can_be_restored(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->post(route('projects.archive', $project))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($owner)->put(route('projects.update', $project), [
            'name' => 'No debe cambiar',
        ])->assertForbidden();

        $this->actingAs($owner)->post(route('projects.restore', $project))
            ->assertRedirect(route('projects.show', $project));

        $this->assertNull($project->fresh()->archived_at);
    }

    /** @return array{User, Project} */
    private function createProject(): array
    {
        $owner = User::factory()->create();
        $project = Project::create([
            'name' => 'Proyecto de prueba',
            'created_by' => $owner->id,
        ]);
        $project->members()->attach($owner->id, ['role' => ProjectRole::Owner->value]);

        return [$owner, $project];
    }
}
