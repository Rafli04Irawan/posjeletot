<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserMenuPermissionTest extends TestCase
{
    public function test_admin_always_has_access_and_user_without_permission_is_denied(): void
    {
        $admin = new User([
            'role' => 'admin',
            'menu_permissions' => [],
        ]);

        $this->assertTrue($admin->hasMenuPermission('produk'));

        $user = new User([
            'role' => 'pegawai',
            'menu_permissions' => ['kasir'],
        ]);

        $this->assertFalse($user->hasMenuPermission('produk'));
    }
}
