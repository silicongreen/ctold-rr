<?php

class InstallController extends Controller
{
    // Welcome page
    
    public function indexAction()
    {
        return $this->render('admin/install.html');
    }
    
    // Wizard
    
    public function wizardAction()
    {
        $config = $this->get('config')->data;
        
        return $this->render('admin/install-wizard.html', compact('config'));
    }
    
    public function wizard2Action()
    {
        $request       = $this->get('request');
        $currentConfig = $this->get('config');
        $config        = $request->postVar('config');
        
        // Validate input
        
        $validationResult = $this->ensureValidConfig();
        
        if(!empty($validationResult))
        {
            // Return special response if data's invalid
            
            return $validationResult;
        }
        
        // Test database connectivity
        
        $dbError = false;
        $dsn     = $currentConfig->data['dbType'] . ':host=' . $config['dbHost'] . ';port=' . $config['dbPort'];
        
        try
        {
            // Fake the non-existing installation, so that no errors are raised by the current configuration
            
            $currentConfig->data['appSettings']['installed'] = false;
            
            if(!$this->get('db')->connect($dsn, $config['dbUser'], $config['dbPassword']))
            {
              
                $dbError = true;
            }
        }
        catch(Exception $ex)
        {
            $dbError = true;
        }

        if($dbError)
        {
            return $this->render('admin/install-wizard.html', compact('config', 'dbError'));
        }
        
        return $this->render('admin/install-wizard-2.html', compact('config'));
    }
    
    public function wizard3Action()
    {
        $request       = $this->get('request');
        $currentConfig = $this->get('config');
        $config        = $request->postVar('config');
        
        // Validate input
        
        $validationResult = $this->ensureValidConfig();
        
        if(!empty($validationResult))
        {
            // Return special response if data's invalid
            
            return $validationResult;
        }
        
        // Update configuration parameters
        
        unset($config['superPassRepeat']);
        
        $configuration = $this->get('config');
        $configuration->updateParameters($config);
        $configuration->updateAppSettings($config['appSettings']);
        $configuration->onRegister(); // force the service to reload
        
        // Install the application
        
        $result = $this->install();

        if(!$result['success'])
        {
            $dbCreateError = true;
            $message       = $result['message'];
            
            return $this->render('admin/install-wizard.html', compact('config', 'dbCreateError', 'message'));
        }
        
        // Test if application was installed correctly (validate database and tables)
        
        $errors = $this->get('model_validation')->validateDb();

        if(count($errors) !== 0)
        {
            $dbCreateError = true;
            $message       = $errors['message'];

            return $this->render('admin/install-wizard.html', compact('config', 'dbCreateError', 'message'));
        }

        // Log

        $this->get('logger')->info('Application installed');

        return $this->render('admin/install-wizard-3.html');
    }
    
    // Uninstall page
    
    public function uninstallAction()
    {
        return $this->render('admin/uninstall.html');
    }

    public function uninstall2Action()
    {
        $request = $this->get('request');
        
        // Ensure POST-only requests

        if(!$request->isPost())
        {
            // Return to the first step
            
            return $this->redirect('Install:uninstall');
        }

        // Uninstall the application
        
        $result = $this->uninstall();
        
        if(!$result['success'])
        {
            $error    = true;
            $errorMsg = $result['errorMsg'];
            
            return $this->render('admin/uninstall.html', compact('error', 'errorMsg'));
        }

        // Log

        $this->get('logger')->info('Application uninstalled');

        return $this->render('admin/uninstall-2.html');
    }
    
    // Helper methods
    
    protected function ensureValidConfig()
    {
        $request = $this->get('request');
        
        if($request->isPost())
        {
            $config     = $request->postVar('config');
            $validators = $this->get('model_validation');
            
            // Validate configuration data
            
            $errors = $validators->validateInstallConfig($config);
            
            if(count($errors) !== 0)
            {
                return $this->render('admin/install-wizard.html', compact('config', 'errors'));
            }
        }
        else
        {
            // Return to the first step
            
            return $this->redirect('Install:wizard');
        }
        
        return null;
    }
    
    protected function install()
    {
        $data = array('message' => '');
        
        if($this->get('request')->isPost())
        {
            $config = $this->get('config');
            
            // Generate the queries
            
            try
            {
                $sql = 'SET CHARACTER SET "utf8";

CREATE DATABASE IF NOT EXISTS %db_name% DEFAULT CHARACTER SET "utf8";

CREATE TABLE IF NOT EXISTS %db_name%.mirrormx_customer_chat_user (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `name` CHAR( 32 ) NOT NULL ,
    `mail` CHAR( 64 ) NOT NULL ,
    `password` CHAR( 255 ) NOT NULL ,
    `image` CHAR( 128 ) NULL ,
    `info` TEXT NULL,
    `roles` CHAR( 128 ) NULL ,
    `last_activity` TIMESTAMP NOT NULL DEFAULT 0
) DEFAULT CHARACTER SET "utf8";

CREATE TABLE IF NOT EXISTS %db_name%.mirrormx_customer_chat_message (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `from_id` BIGINT NOT NULL ,
    `to_id` BIGINT NOT NULL ,
    `body` TEXT NOT NULL ,
    `datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `talk_id` BIGINT NOT NULL,
    `is_new` CHAR(1) NOT NULL DEFAULT "y",
    `from_user_info` TEXT NOT NULL,
    `to_user_info` TEXT NOT NULL
) DEFAULT CHARACTER SET "utf8";

CREATE TABLE IF NOT EXISTS %db_name%.mirrormx_customer_chat_data (
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `type` VARCHAR(255) NOT NULL ,
    `key` VARCHAR(255) NOT NULL ,
    `value` TEXT ,
    KEY `type` (`type`) ,
    KEY `key` (`key`)
) DEFAULT CHARACTER SET "utf8";';
                //$sql = file_get_contents(ROOT_DIR . '/../sql/install_' . $config->data['dbType'] . '.sql');
                $sql = str_replace('%db_name%', $config->data['dbName'], $sql);

                // Fake the non-existing installation, so that no errors are raised by the current configuration
            
                $config->data['appSettings']['installed'] = false;
                
                // Create the database and tables

                $data['success'] = $this->get('db')->execute($sql);
            }
            catch(Exception $e)
            {
                print_r($e);
                $data['success']  = false;
                $data['message'] = $e->getMessage();
                exit;
            }
            
            if($data['success'])
            {
                $config->updateAppSettings(array('installed' => true));
            }
            else
            {
                if(empty($data['message']))
                {
                    $data['message'] = 'Other error';
                }
            }
        }
        
        return $data;
    }

    protected function uninstall()
    {
        $data = array();
        
        if($this->get('request')->isPost())
        {
            $config = $this->get('config');
            
            // Generate the queries
            
            try
            {
                $sql = file_get_contents(ROOT_DIR . '/../sql/uninstall_' . $config->data['dbType'] . '.sql');
                $sql = str_replace('%db_name%', $config->data['dbName'], $sql);

                // Delete the whole database

                $data['success'] = @$this->get('db')->execute($sql);
            }
            catch(Exception $e)
            {
                $data['success']  = false;
                $data['errorMsg'] = $e->getMessage();
            }
            
            if($data['success'])
            {
                $config = $this->get('config');
                $config->updateAppSettings(array('installed' => false));
            }
            else
            {
                $data['error'] = 'There was an error during the uninstallation';
            }
        }

        return $data;
    }
}

?>
