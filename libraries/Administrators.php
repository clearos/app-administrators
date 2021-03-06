<?php

/**
 * Administrators class.
 *
 * @category   apps
 * @package    administrators
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/administrators/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\administrators;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('administrators');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\mode\Mode_Engine as Mode_Engine;
use \clearos\apps\mode\Mode_Factory as Mode_Factory;
use \clearos\apps\policy_manager\Policy as Policy;
use \clearos\apps\policy_manager\Policy_Manager as Policy_Manager;
use \clearos\apps\users\User_Factory as User_Factory;

clearos_load_library('base/Engine');
clearos_load_library('mode/Mode_Engine');
clearos_load_library('mode/Mode_Factory');
clearos_load_library('policy_manager/Policy');
clearos_load_library('policy_manager/Policy_Manager');
clearos_load_library('users/User_Factory');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Administrators class.
 *
 * @category   apps
 * @package    administrators
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/administrators/
 */

class Administrators extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Administrators constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns list of all installed apps.
     *
     * @return array list of all installed apps
     * @throws Engine_Exception
     */

    public function get_all_apps()
    {
        clearos_profile(__METHOD__, __LINE__);

        // In master/slave mode, we want a list of all apps installed on all the nodes.

        $mode_object = Mode_Factory::create();
        $mode = $mode_object->get_mode();

        if (clearos_app_installed('central_management') && $mode === Mode_Engine::MODE_MASTER) {
            clearos_load_library('central_management/Device_Manager');

            $device_manager = new \clearos\apps\central_management\Device_Manager();
            $raw_apps = $device_manager->get_installed_apps();
        } else {
            // The code is part of the framework (shared/libraries/Apps)
            $raw_apps = clearos_get_apps();
        }

        // Order alphabetically for now
        //------------------------------

        foreach ($raw_apps as $basename => $details) {
            // Skip apps designed for all users (e.g. User Profile, User Certificates)
            if ($details['user_access'] === TRUE)
                continue;

            $key = $details['category'] . '.' . $details['subcategory'] . '.' . $details['name'];
            $sorted_keys[$key] = $basename;
        }

        ksort($sorted_keys);

        foreach ($sorted_keys as $key => $basename)
            $sorted_apps[$basename] = $raw_apps[$basename];

        return $sorted_apps;
    }

    /**
     * Returns list of permitted apps for given policy.
     * 
     * @param string $policy policy name
     *
     * @return array list of permitted apps
     * @throws Engine_Exception
     */

    public function get_policy_apps($policy)
    {
        clearos_profile(__METHOD__, __LINE__);

        $policy_object = new Policy();

        $details = $policy_object->get('administrators', $policy);

        return $details['settings'];
    }

    /**
     * Returns list of permitted apps for given user.
     * 
     * @param string $username username
     *
     * @return array list of permitted apps
     * @throws Engine_Exception
     */

    public function get_user_apps($username)
    {
        clearos_profile(__METHOD__, __LINE__);

        if ($username === 'root')
            return array();

        $policy_manager = new Policy_Manager();
        $user = User_Factory::create($username);

        $apps = array();
        $policies = $policy_manager->get_policies('administrators');
        $group_memberships = $user->get_group_memberships();

        foreach ($policies as $policy => $details) {
            if (in_array($details['group'], $group_memberships)) {
                $policy_apps = $this->get_policy_apps($policy);
                $apps = array_merge($apps, $policy_apps);
            }
        }

        return array_unique($apps);
    }

    /**
     * Sets an administrators policy.
     *
     * @param string $name policy name
     * @param array  $apps apps list
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_policy($name, $apps)
    {
        clearos_profile(__METHOD__, __LINE__);

        $app_list = array();

        foreach ($apps as $app => $state)
            $app_list[] = $app;

        $policy = new Policy();
        $policy->store_settings('administrators', $name, $app_list);
    }
}
