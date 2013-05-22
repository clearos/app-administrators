<?php

/**
 * Administrators policy controller.
 *
 * @category   Apps
 * @package    Administrators
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/administrators/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

if (file_exists(clearos_app_base('policy_manager') . '/controllers/groups.php'))
    require clearos_app_base('policy_manager') . '/controllers/groups.php';
else
    require clearos_app_base('groups') . '/controllers/groups.php';

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Administrators policy controller.
 *
 * @category   Apps
 * @package    Administrators
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/administrators/
 */

class Policy extends Groups
{
    /**
     * Administrators policy constructor.
     */

    function __construct()
    {
        parent::__construct('administrators', array('administrators_plugin'));
    }

    /**
     * Edit policy view.
     *
     * @param string $policy policy
     *
     * @return view
     */

    function configure($policy)
    {
        // Load libraries
        //---------------

        $this->lang->load('administrators');
        $this->load->library('base/Access_Control');

        // Load the view data 
        //------------------- 

        try {
            // $configuration = $this->dansguardian->get_policy_configuration($policy);
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        $data['policy'] = $policy;
        $data['name'] = $configuration['groupname'];

        // Load the views
        //---------------

        $this->page->view_form('administrators/policy', $data, lang('base_configure_policy'));
    }
}
