# PHP File Uploading Library
A PHP library for uploading files to the server

### Downloading and implementation 
- Download the zip file manually and include in your project directory 
  **OR**
   Using [Composer](https://getcomposer.org/), run command 
  `composer require muhammadsiyab/file_upload` <br /> <br />
- Include library in your script  
    ```php
    require_once './vendor/autoload.php'; 
    ```

### Uploading file
```php 
<?php

// Array containing custom messages 
// (Optional parameter, if not passed, default error messages will be used)

$custom_messages = [
    'types' => 'The file type is not allowed',
    'max_size' => 'The file size must not be greater than :size kb'
];


if ( isset($_POST['submit']) ) {
    
    // Instantiate `Upload` Class
    $upload = new FileUpload\Upload('field_name', [
        'upload_dir' =>   'uploads/',
        'max_size'   =>   100,
        'types'      =>   'png|jpg|jpeg',
    ], $custom_messages);


    // Check whether the uploading is done or not 
    if ( $upload->is_uploaded() == TRUE ) {
        // File uploaded successfully
        echo 'File uploaded';
    } else {
        // Display errors
        echo $upload->display_errors();
    }
}
```


### Available methods
#### 1. is_uploaded
Checks whether the file upload is done or not
###### Example:
``` php
if ( $upload->is_uploaded() == TRUE ) {
    // File upload success
} else {
    // File upload failure
}
```

#### 2. display_errors
Displays all upload errors
###### Example
``` php
echo display_errors();
```

#### 3. formatted_errors
Displays all upload errors with custom formatting
###### Parameters:
- String `$start_tag` 
     ~ Starting Tag (can be along with some styles or classes) 
- String `$end_tag` 
     ~ Ending Tag
###### Example:
``` php
echo formatted_errors('<div class="errors">', "</div>");
```

#### 4. bootstrap_errors
Displays all upload errors with bootstrap alerts 
> <small>([Bootstrap's](https://getbootstrap.com) CSS is required for this) </small>

###### Example:
``` php
echo bootstrap_errors();
```

### Available Preferences

| Name           |                    Description                     |   Default   |  Syntax
|--------------- |----------------------------------------------------|------------ | ------------
| `upload_dir`     |  The directory in which the file will be stored    |     -       |  `'upload_dir'     =>   'your_directory/'`
| `types`          |  The file types you want to allow                  |     -       |  `'types'          =>   'jpg|png|gif/'`
| `max_size`       |  Maximum size of the file (in Kilobytes)           |     -       |  `'max_size'       =>   200`
| `min_dimension`  |  Minimum dimension of the image                    |     -       |  `'min_dimension'  =>   '300*300'`
| `max_dimension`  |  Maximum dimension of the image                    |     -       |  `'max_dimension'  =>   '800*800'` 
| `unique_name`    |  Sets unique name of the file by using timestamps  |    `true`   |  `'unique_name'    =>   true`
| `name_uppercase` |  Sets uppercase name for the file                  |    `false`  |  `'name_uppercase' =>   false`
| `no_spaces`      |  Replaces spaces in file name with hypens (-)      |    `false`  |  `'no_spaces'      =>   false`
