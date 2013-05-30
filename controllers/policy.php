<?php

/**
 * Administrators policy controller.
 *
 * @category   apps
 * @package    administrators
 * @subpackage controllers
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

require clearos_app_base('policy_manager') . '/controllers/policy_controller.php';

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Administrators policy controller.
 *
 * @category   apps
 * @package    administrators
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/administrators/
 */

class Policy extends Policy_Controller
{
    /**
     * Administrators policy constructor.
     */

    function __construct()
    {
        parent::__construct('administrators');
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
        $this->load->library('administrators/Administrators');

        // Handle form submit
        //-------------------

        if ($this->input->post('submit')) {
            try {
                $this->administrators->set_policy($policy, $this->input->post('apps'));

                $this->page->set_status_updated();
                redirect('/administrators/policy');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
            $data['policy'] = $policy;
            $data['all_apps'] = $this->administrators->get_all_apps();
            $data['apps'] = $this->administrators->get_policy_apps($policy);
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load the views
        //---------------

        $this->page->view_form('administrators/policy', $data, lang('base_configure_policy'));
    }
}
