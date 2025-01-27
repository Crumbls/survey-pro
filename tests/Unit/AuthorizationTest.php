<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Ability;
use App\Authorization\Bouncer;
//use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorizationTest extends TestCase
{
//    use RefreshDatabase;

    protected User $user;
    protected Role $role;
    protected Ability $ability;
    protected int $tenantId;
    protected Bouncer $bouncer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->role = Role::create(['name' => 'admin', 'title' => 'Administrator']);
        $this->ability = Ability::create(['name' => 'edit-posts']);
        $this->tenantId = 1;
        $this->bouncer = new Bouncer($this->tenantId);
    }

    /** @test */
    public function it_can_assign_role_to_user()
    {
        $this->bouncer->assign($this->role->id, $this->user);

        $this->assertTrue($this->bouncer->hasRole($this->role->id, $this->user));
    }

    /** @test */
    public function it_can_assign_ability_to_role()
    {
        $this->role->abilities()->attach($this->ability);

        $this->assertTrue($this->role->abilities->contains($this->ability));
    }

    /** @test */
    public function it_can_check_user_has_role_through_tenant()
    {
        $this->bouncer->assign($this->role->id, $this->user);

        $roles = $this->bouncer->getRoles($this->user);

        $this->assertCount(1, $roles);
        $this->assertEquals($this->role->id, $roles->first()->role_id);
    }

    /** @test */
    public function it_can_retract_role_from_user()
    {
        $this->bouncer->assign($this->role->id, $this->user);
        $this->bouncer->retract($this->role->id, $this->user);

        $this->assertFalse($this->bouncer->hasRole($this->role->id, $this->user));
    }

    /** @test */
    public function it_caches_user_roles()
    {
        $this->bouncer->assign($this->role->id, $this->user);

        // First call should cache
        $roles = $this->bouncer->getRoles($this->user);

        // Delete directly to bypass cache
        \DB::table('tenant_user_role')->delete();

        // Should still get roles from cache
        $cachedRoles = $this->bouncer->getRoles($this->user);

        $this->assertEquals($roles->count(), $cachedRoles->count());
    }
}
