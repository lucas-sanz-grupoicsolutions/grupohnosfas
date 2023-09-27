<?php

namespace App\Validation;

use App\Models\UsersModel;

class UserRules
{
    /**
     * > If the user is not found in the database, return false
     *
     * @param string str The string to validate
     * @param string field The name of the field to validate.
     * @param array data The data array that was passed to the validator.
     *
     * @return bool boolean value.
     */
    public function ensureUserExist( string $str, string $field)
    {
            $model = new UsersModel();
            if (! $model->getUserBy($field,$str)){
                return false;
            }
            return true;
    }

      public function ensureUserNotExist( string $str, string $field)
    {
            return !$this->ensureUserExist($str, $field);
    }

    /**
     * It takes the password from the form, gets the user from the database, and compares the password from the form with
     * the password from the database
     *
     * @param string str The string to validate
     * @param string field The name of the field being validated.
     * @param array data The data array that was submitted to the form.
     *
     * @return bool boolean value.
     */
    public function validatePassword(string $str, string $field, array $data)
    {

        try {
            $model = new UsersModel();
            $user = $model->getUserByUserName($data['mail']);
            return password_verify($data['password'], $user->password);
        } catch (\Throwable $th) {
            return false;
        }

    }
}