# Webkul-Create-Opencart-Setup
This console app used to create a new Opencart setup through command line. In this app we used symfony/console.

## How to Configure

### Prerequisite
Before using this app, you have to follow below steps:
- Upload app, project directories and composer.json file into your opencart root directory (path where you want to create opencart setup).
- Go to the path where you uploaded the above directories and file by using terminal.
- Now install the composer by executing below command from terminal:
    - Syntax:  curl -s http://getcomposer.org/installer | php
    - Syntax:  php composer.phar install
- After successfully installation of conposer, you will see "vendor" and "composer.phar" directory and file (PHP Archive).

### How to use
By using this console app you can create Opencart setup for the 2.x.x.x version.
- Step1: By using terminal go to the path where you upload the app, project directories and run the below command to start the app:
    - Syntax:  app/console app:create-oc-setup
- Step2: Now you have to provide opencart version that you want to install.
    - Supported Oc Version: 2.0.0.0 , 2.0.1.0 , 2.0.1.1 , 2.0.2.0 , 2.0.3.1 , 2.1.0.1 , 2.1.0.2 , 2.2.0.0 , 2.3.0.0 , 2.3.0.1 , 2.3.0.2
- Step3: Now system will ask you to create separate directory for opencart setup (Y/N):
    - Provide 'y' and 'Y' if you want to create opencart setup inside a directory.
    - Provide 'n' and 'N' if you don't want to create a directory.
- Step4: If you choose 'y' or 'Y', then you have to provide the directory path for opencart setup:
    - If you want to create the setup inside the current directory, then provide directory name like:
        - Syntax: Opencart , /Opencart/ , /Opencart/2_0/
    - If you want to create the opencart setup outside the current directory (i.e. parallel to app, project or parent directory) then provide directory name like:
        - Syntax: /../Opencart , /../Opencart/ , /../Opencart/2_0/
- Step5: After this you have to provide the correct database details:
    - Hostname                  (e.g. localhost)
    - Database Username         (e.g. root)
    - Database Password         (e.g. password)
    - Database Name             (e.g. johndoe_db1)
    - Port Number               (e.g. 3306)
    - Database Prefix           (e.g. oc_)
    - Opencart Admin Username   (e.g. demo)
    - Opencart Admin Password   (e.g. demo)
    - Opencart Admin Email-Id   (e.g. johndoe@example.com)
    - Store URL for config file (e.g. http://localhost/Opencart/2_0/ , https://xyz.com/opencart/)
- Step6: After providing all the correct detail, you will get success message with admin and catalog url.
