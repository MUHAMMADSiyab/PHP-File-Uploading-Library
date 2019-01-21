<?php

    /** 
       * @package File Uploading Library
       * @version 1.0
       * @author MUHAMMAD Siyab
       * @link https://gituhub.com/MUHAMMADSiyab/PHP-File-Uploading-Library 
       * @license MIT
    **/

    namespace FileUpload;

    class Upload {

        // Variables declaration
        public $fileName = null;
        public $fileType = null;
        public $fileSize = null;
        public $tmpName = null;
        public $uploadDir = null;
        public $acceptedTypes = null;
        public $maxSize = null;
        public $errors = [];
        public $config;
        public $custom_messages;

        
        /** 
         * @method __construct
         * @return void
         * @param string $field 
         *      Name of the field
         * @param array $config 
         *      Array containing upload confiuration i.e upload_path, max_size etc.
         * 
        */

        public function __construct (String $field, Array $config, Array $custom_messages = null) {
            
            $this->config = $config;

            // Check whether the file is selected or not
            if ($_FILES[$field]['name'] === '') {
                // Stop execution 
                exit('No file selected');
            } else {
                // Get file array
                $this->file = $_FILES[$field];

                // Get data from file array
                $this->fileName = $this->file['name'];
                $this->fileType = $this->file['type'];
                $this->fileSize = $this->file['size'];
                $this->tmpName = $this->file['tmp_name'];   
                $this->dimension = getimagesize($this->tmpName);  
                // Get width & height from dimension
                $this->width = $this->dimension[0];
                $this->height = $this->dimension[1]; 


                $this->uploadDir = @$this->config['upload_dir'];
                $this->acceptedTypes = explode('|', @$this->config['types']);
                $this->maxSize = @$this->config['max_size'];


                // If `custom messages` array is passed
                if (is_array($custom_messages) && $custom_messages !== null) {
                    $this->custom_messages = $custom_messages;
                }

            }  
        }

        // -----------------------------------------------------------------

        /** 
         * @method directory_exists
         *      Checks whether the specified directory exists or not
         * @return void
        */
        
        public function directory_exists () {
            if (! array_key_exists('upload_dir', $this->config)) {
                return false;
            }
            // Check whether the specified directory exists or not
            if (file_exists($this->uploadDir)) {
                return true;
            } 
            return false;
        }

        // -----------------------------------------------------------------

        /** 
         * @method checkFileType
         *      Checks whether the file contains the valid type or not 
         * @return void
        */
       
        public function checkFileType () {
            // Checks if the file has the valid type
            if (! in_array(explode('/', $this->fileType)[1], $this->acceptedTypes)) {
                $custom_message = @$this->custom_messages['types'];
                $default_message = 'File type is not allowed';
                $this->setMessages('types', $custom_message, $default_message);
            }
    
            
        }

        // -----------------------------------------------------------------

        /** 
         * @method checkFileSize
         *      Checks whether the file is of the allowed size 
         * @return void
        */       
        
        public function checkFileSize () {
            // Check if the file is of the allowed size
            if ($this->fileSize > $this->maxSize * 1000) {
                $custom_message = str_replace(':size', $this->maxSize, @$this->custom_messages['max_size']);
                $default_message = 'File size must be less than or equal to ' . $this->maxSize. ' kb';
                $this->setMessages('max_size', $custom_message, $default_message);
            }
        }
        
        // -----------------------------------------------------------------

        /** 
         * @method checkMinImageDimension
         *      Checks the minimum dimension of image
         * @return void
        */
        
        public function checkMinImageDimension () {
            // Minimum image dimension set
            $minDimension = explode('*', $this->config['min_dimension']);
            $minWidth = $minDimension[0];
            $minHeight = $minDimension[1];

            // Check 
            if (($this->width < $minWidth && $this->height < $minHeight) || ($this->width < $minWidth || $this->height < $minHeight)) {
                $custom_message = str_replace(':min_dimension', $this->config['min_dimension'], @$this->custom_messages['min_dimension']);
                $default_message = 'The image dimension must not be less than the minimum dimension of ' . $this->config['min_dimension'] . ' pixels';
                $this->setMessages('min_dimension', $custom_message, $default_message);
            }   
        }
        
        // -----------------------------------------------------------------

        /** 
         * @method checkMaxImageDimension
         *      Checks the maximum dimension of image
         * @return void
        */
        
        public function checkMaxImageDimension () {
            // Maximum image dimension set
            $maxDimension = explode('*', $this->config['max_dimension']);
            $maxWidth = $maxDimension[0];
            $maxHeight = $maxDimension[1];

            // Check 
            if (($this->width > $maxWidth && $this->height > $maxHeight) || ($this->width > $maxWidth || $this->height > $maxHeight)) {
                $custom_message = str_replace(':max_dimension', $this->config['max_dimension'], @$this->custom_messages['max_dimension']);
                $default_message = 'The image dimension must not exceed the maximum dimension of ' . $this->config['max_dimension'] . ' pixels';
                $this->setMessages('max_dimension', $custom_message, $default_message);
            }   
        }
        
        // -----------------------------------------------------------------

        /** 
         * @method nameUpperCase
         *      Transforms the file name to uppercase letters 
         * @return void
        */
        
        public function nameUpperCase () {
            // Transform name to uppercase
            $this->fileName = strtoupper($this->fileName);
        }
        
        // -----------------------------------------------------------------

        /** 
         * @method noSpaces
         *      Replaces spaces with hypens in filename
         * @return void
        */
        
        public function noSpaces () {
            // Replace spaces with hypens
            $this->fileName = str_replace(' ', '-', $this->fileName);
        }
          
        // -----------------------------------------------------------------

        /** 
         * @method setMessages
         *      Sets error messages
         * @return void
        */
        
        public function setMessages ($key, $custom_message, $default_message) {
            if (array_key_exists($key, $this->custom_messages)) {
                // Push custom error to errors array
                array_push($this->errors, $custom_message);
            } else {
                // Push default error to errors array
                array_push($this->errors, $default_message);
            }
        }
    
        // -----------------------------------------------------------------

        /** 
         * @method uploadAfterCheck
         *      Finalizes upload after all checks
         * @return boolean
         *      Returns true on success 
        */

        public function uploadAfterCheck () {            
            // Check if directory exists
            if ($this->directory_exists()) {

                // If `types` are passed via config array
                if (array_key_exists('types', $this->config)) {
                    $this->checkFileType();
                } 
                // If `max_size` is passed via config array
                if (array_key_exists('max_size', $this->config)) {
                    $this->checkFileSize();
                }
                // If `min_dimension` is passed via config array
                if (array_key_exists('min_dimension', $this->config)) {
                    if (explode('/', $this->fileType)[0] == 'image') {
                        $this->checkMinImageDimension();
                    }
                }
                // If `max_dimension` is passed via config array
                if (array_key_exists('max_dimension', $this->config)) {
                    if (explode('/', $this->fileType)[0] == 'image') {
                        $this->checkMaxImageDimension();
                    }
                }
                // If `name_uppercase` is passed via config array
                if (array_key_exists('name_uppercase', $this->config) && $this->config['name_uppercase'] == true) {
                    $this->nameUpperCase();
                }
                // If `no_spaces` is passed via config array
                if (array_key_exists('no_spaces', $this->config) && $this->config['no_spaces'] == true) {
                    $this->noSpaces();
                }


                // In case of `no errors`
                if (count($this->errors) == 0) {
                    $this->doUpload();
                    return true;
                } else {
                    return false;
                }
            } else {
                // Stop the execution of the script
                 exit('No upload path is provided or path doesn\'t exists');
            }


        }

        // -----------------------------------------------------------------

        /** 
         * @method doUpload
         *      Uploads the file 
         * @return boolean
         *      Returns true on success 
        */
        
        
        public function doUpload () {
            if (! array_key_exists('unique_name', $this->config) || $this->config['unique_name'] == true) {
                // Upload the file without overwriting existing 
                if (move_uploaded_file($this->tmpName, $this->uploadDir . '/' . time() . '_' . $this->fileName)) {
                    return true;
                }
                return false;
            } else {
                // Upload the file overwriting existing one
                if (move_uploaded_file($this->tmpName, $this->uploadDir . '/' . $this->fileName)) {
                    return true;
                }
                return false;
            }
        }   
        
        // -----------------------------------------------------------------

        /** 
         * @method is_uploaded
         *      Checks whether the file upload is done or not
         * @return boolean
         *      Returns true on success and false on failure
        */
        
        public function is_uploaded () {
            if ($this->uploadAfterCheck() == true) {
                return true;
            }
            return false;
        }

        // -----------------------------------------------------------------

        /** 
         * @method display_errors
         *      Shows the upload errors
         * @return void
        */
        
        public function display_errors () {
           foreach($this->errors as $error) {
                echo $error  . '<br />';
           }
        }  

        // -----------------------------------------------------------------

        /** 
         * @method formatted_errors
         *      Shows the formatted upload errors
         * @param $start_tag
         *      Starting HTML Tag (May be along with some styles or classes)
         * @param $end_tag
         *      Ending HTML Tag 
         * @return void
        */

        public function formatted_errors ($start_tag, $end_tag) {
            foreach($this->errors as $error) {
                echo $start_tag . $error . $end_tag . '<br />';
           }
        }
        
        // -----------------------------------------------------------------

        /** 
         * @method bootstrap_errors
         *      Shows the upload errors in bootstrap alert style
         * @return void
        */

        public function bootstrap_errors () {
            foreach($this->errors as $error) {
                echo '<div class="alert alert-danger">' . $error . '</div>';
           }
        }




    }


    