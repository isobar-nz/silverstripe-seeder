<?php

namespace Seeder;

use Exception;
use Faker\Factory;
use Member;

/**
 * Class MemberProvider
 */
class MemberProvider extends Provider
{
    /**
     * @var
     */
    private $faker;

    /**
     * @var string
     */
    public static $shorthand = 'Member';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    /**
     * @param $argumentString
     * @return array
     * @throws Exception
     */
    public static function parseOptions($argumentString)
    {
        $args = array_map(function ($arg) {
            return trim($arg);
        }, explode(',', $argumentString));

        if (count($args) < 2) {
            throw new Exception('member provider requires an email and password to be passed as options');
        }

        $options = array(
            'email' => $args[0],
            'password' => $args[1],
        );

        if (count($args) >= 3) {
            $options['group'] = $args[2];
        }

        return $options;
    }

    /**
     * @param $field
     * @param $state
     * @throws Exception
     * @returns null
     */
    protected function generateField($field, $state)
    {
        throw new Exception('member provider does not support generating db fields');
    }

    /**
     * @param $field
     * @param $upState
     * @return mixed
     * @throws Exception
     */
    protected function generateOne($field, $upState)
    {
        if (empty($field->options['email'])) {
            throw new Exception('member provider requires an email');
        }

        if (empty($field->options['password'])) {
            throw new Exception('member provider requires a password');
        }

        $memberClassName = $field->dataType;
        $member = new $memberClassName();
        $member->FirstName = $this->faker->firstName();
        $member->Surname = $this->faker->lastName;

        $email = $field->options['email'];
        $parts = explode('@', $email);

        // find unique email address
        $member->Email = $email;
        $counter = 0;
        while (Member::get()->filter('Email', $member->Email)->first()) {
            $email = $parts[0] . $counter . '@' . (isset($parts[1]) ? $parts[1] : 'domain.com');
            $member->Email = substr($email, 0, 60);
            $counter++;
        }

        $member->Password = $field->options['password'];


        $this->writer->write($member, $field);

        if (isset($field->options['group'])) {
            $code = $field->options['group'];
            $member->onAfterExistsCallback(function ($member) use ($code) {
                $member->addToGroupByCode($code);
            });
        }

        return $member;
    }
}
