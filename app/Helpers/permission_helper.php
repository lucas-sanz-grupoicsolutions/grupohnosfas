<?php

/**
 * It loads the permissions of a user from the database and returns them as an array
 *
 * @param int idUser The user ID
 *
 * @return array An array of permissions.
 */
function loadPermissions(int $idUser): array
{
    $arrayPermission = [];
    $permissionModel = model('PermissionModel');
    $permissions = $permissionModel->getPermissionAll('id_user', $idUser);

    foreach ($permissions as $permission) {
        $arrayPermission[$permission->key] = $permission->value;
    }
    return array_merge(listPermis(), $arrayPermission);
}

/**
 * It checks if the user is logged in, if the user is logged in, it checks if the user has the permission to access the
 * page
 *
 * @param array perms The permission you want to check.
 * @param array allPerms This is an array of all the permissions that the user has.
 */
function checkPermission(array $perms, array $allPerms)
{
    if (!empty(session()->idUser)) {
        if (array_key_exists($perms[0], $allPerms)) {
            return $allPerms[$perms[0]];
        }
    }
    return 'notPermission';
}

// Listado de permisos por defecto

function listPermis(): array
{
    return [
        'show' => '1',
        'delete' => '0',
        'edit' => '0',
        'create' => '0'
    ];

}

