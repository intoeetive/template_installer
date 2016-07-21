<?php

/*
=====================================================
 Template Installer
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2014 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
*/

if ( ! defined('BASEPATH'))
{
	exit('Invalid file request');
}

require_once PATH_THIRD.'template_installer/config.php';

class Template_installer_upd {

    var $version = TEMPLATE_INSTALLER_ADDON_VERSION;
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    function install() { 
		
		$this->EE->load->dbforge(); 

        $data = array( 'module_name' => 'Template_installer' , 'module_version' => $this->version, 'has_cp_backend' => 'y', 'has_publish_fields' => 'n'); 
        $this->EE->db->insert('modules', $data); 
        
        return TRUE; 
        
    } 
    
    
    function uninstall() { 

        $this->EE->load->dbforge(); 
		
		$this->EE->db->select('module_id'); 
        $query = $this->EE->db->get_where('modules', array('module_name' => 'Template_installer')); 
        
        $this->EE->db->where('module_id', $query->row('module_id')); 
        $this->EE->db->delete('module_member_groups'); 
        
        $this->EE->db->where('module_name', 'Template_installer'); 
        $this->EE->db->delete('modules'); 
        
        $this->EE->db->where('class', 'Template_installer'); 
        $this->EE->db->delete('actions'); 

        return TRUE; 
    } 
    
    function update($current='') 
	{ 
		return TRUE; 
    } 
	

}
/* END */
?>