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

class Template_installer_mcp {

    var $version = TEMPLATE_INSTALLER_ADDON_VERSION;
    
    var $settings = array();
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    function index()
    {
        $data['dir_path'] = PATH_THEMES.'site_themes/default/';
        
        $css = "
			<style type='text/css'>
			#ti_warning{
				border:1px solid #bf0012;
				padding:15px 45px 15px 15px;
				color:#18362D;
				font-size:14px;
				margin:0 0 10px 0;
			}
			#ti_warning a{
				color:#a10a0a;
			}
			#ti_warning h4{
				color:#18362D;
				font-size:18px;
				font-weight:bold;
				display:inline;
				margin:0 8px 0 0;
			}

			</style>
			"; 
        
        $this->EE->cp->add_to_head($css);
        
    	return $this->EE->load->view('index', $data, TRUE);
	
    }
    
    
    
    function install()
    {
		if (empty($_POST))
    	{
    		show_error($this->EE->lang->line('unauthorized_access'));
    	}
        
        //check file dir is provided and exists
        if ($this->EE->input->post('dir_path')=='' || $this->EE->input->post('dir_path')=='')
        {
            show_error($this->EE->lang->line('directory_error'));
        }
        
        $root_dir = rtrim($this->EE->input->post('dir_path'), '/').'/';
        
        if (!is_dir($root_dir))
        {
            show_error($this->EE->lang->line('directory_error'));
        }
        

		$default_group = 'site';

		$template_preferences = array(
			'allow_php'         => 'n',
            'php_parse_location'=> 'o',
            'cache'			    => 'n',
			'refresh'		    => 0
		);

		$no_access = array(
			'2' 
		);


        $installed_count = 0;
		$template_groups = array();
        $i = 0;

		$allowed_suffixes = array('html', 'htm', 'webpage', 'php', 'css', 'xml', 'feed', 'rss', 'atom', 'static', 'txt', 'js');

		$template_type_conversions = array(
			'txt'  => 'static',
			'rss'  => 'feed',
			'atom' => 'feed',
			'html' => 'webpage',
            'htm'  => 'webpage',
			'php'  => 'webpage',
		);

		if ($fp = opendir($root_dir))
		{
			while (FALSE !== ($folder = readdir($fp)))
			{
				if ($folder!='.' && $folder!='..' && is_dir($root_dir.$folder))
				{
                    ++$i;

                    $group = preg_replace("#[^a-zA-Z0-9_\-/\.]#i", '', $folder);
                    //echo '<strong>'.$group.'</strong>'.BR;
                    //does group with same name exist? rename it
                    $check_q = $this->EE->db->select('group_id')
                                ->from('template_groups')
                                ->where('group_name', $group)
                                ->where('site_id', $this->EE->config->item('site_id'))
                                ->get();
                    if ($check_q->num_rows()>0)
                    {
                        $up_data = array('group_name' => $group.'_'.$this->EE->localize->now);
                        $this->EE->db->where('group_id', $check_q->row('group_id'));
                        $this->EE->db->update('template_groups', $up_data);
                    }

					$data = array(
						'group_name'		=> $group,
                        'site_id'           => $this->EE->config->item('site_id'),
						'group_order'		=> $i,
						'is_site_default'	=> ($default_group == $group) ? 'y' : 'n'
					);

					$this->EE->db->insert('template_groups', $data);
                    $group_id = $this->EE->db->insert_id();

					$templates = array('index' => 'index.html');
                    
					if ($tmpls_fp = opendir($root_dir.$folder))
					{
						$dir_installed_count = 0;
                        while (FALSE !== ($file = readdir($tmpls_fp)))
						{
							if (@is_file($root_dir.$folder.'/'.$file) && $file != '.DS_Store' && $file != '.htaccess')
							{
								//echo $file.BR;
                                $name	= $file;
                                if (strpos($file, '.') === FALSE)
        						{
        							$type	= 'webpage';
        						}
        						else
        						{
        							$type = $ext = strtolower(ltrim(strrchr($file, '.'), '.'));
                                    
                                    if ( ! in_array($type, $allowed_suffixes))
        							{
        								continue;
        							}
        
        							if (isset($template_type_conversions[$type]))
        							{
        								$type = $template_type_conversions[$type];
        							}
                                    
                                    
                                    if ($type=='webpage' || $type=='static')
                                    {
        							 $name	= preg_replace("#[^a-zA-Z0-9_\-/\.]#i", '', substr($file, 0, -(strlen($ext) + 1)));
                                    }
        							
        						}
                                
                                $data = array(
        							'group_id'			=> $group_id,
        							'template_name'		=> $name,
        							'template_type'		=> $type,
        							'template_data'		=> file_get_contents($root_dir.$folder.'/'.$file),
        							'edit_date'			=> $this->EE->localize->now,
        							'last_author_id'	=> $this->EE->session->userdata('member_id'),
        						);
        
        						$data = array_merge($data, $template_preferences);
                        
                                $this->EE->db->insert('templates', $data);
                                $template_id = $this->EE->db->insert_id();
                                
                                foreach ($no_access as $na_group_id)
                                {
                                    $this->EE->db->insert('template_no_access',  array('template_id' => $template_id, 'member_group' => $na_group_id));
                                }
                                
                                $installed_count++;
                                $dir_installed_count++;
							}
						}

						@closedir($tmpls_fp);
                        
                        if ($dir_installed_count==0)
                        {
                            //remove the template group, if we did not create any templates
                            $this->EE->db->where('group_id', $group_id);
                            $this->EE->db->delete('template_groups');
                        }
					}

				}
			}

			closedir($fp);
            
            //um, oh, do we have default template group?
            $check_q = $this->EE->db->select('group_id')
                    ->from('template_groups')
                    ->where('is_site_default', 'y')
                    ->get();
            if ($check_q->num_rows()==0)
            {
                $this->EE->db->update('template_groups', array('is_site_default' => 'y'), NULL, 1);
            }
            
		}

		if ($installed_count>0)
        {
            $this->EE->session->set_flashdata('message_success', lang('success_message'));
        }
        else
        {
            $this->EE->session->set_flashdata('message_failure', lang('fail_message'));
        }
        
        $this->EE->functions->redirect(BASE.AMP.'C=design'.AMP.'M=manager');
        
	}
    
   
    
  
  

}
/* END */
?>