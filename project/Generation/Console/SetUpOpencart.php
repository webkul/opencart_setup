<?php
namespace OcCommands;
/**
 * to download the opencart zip and create the opencart setup
 */
 use Symfony\Component\Console\Command\Command;
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Output\OutputInterface;
 use Symfony\Component\Console\Formatter\OutputFormatterStyle;
 use Symfony\Component\Console\Question\Question;

class SetUpOpencart extends Command
{

      private $data = array();

      private $newDirPath = '';

      private $inputData = array();

      private $opencartVersion = array(
        '2.0.0.0',
        '2.0.1.0',
        '2.0.1.1',
        '2.0.2.0',
        '2.0.3.1',
        '2.1.0.1',
        '2.1.0.2',
        '2.2.0.0',
        '2.3.0.0',
        '2.3.0.1',
        '2.3.0.2',
      );

      private $confirmAnswer = array(
        'y',
        'Y',
        'n',
        'N'
      );

      protected function configure()
      {
            $this
            // the name of the command (the part after "bin/console")
            ->setName('app:create-oc-setup')

            // the short description shown while running "php bin/console list"
            ->setDescription('Download Opencart and Setup Opencart with Database.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allow you to download and install the opencart...');
      }

      protected function execute(InputInterface $input, OutputInterface $output)
      {

          require_once('language/english/language.php');
          $this->data = $data;
          // outputs multiple lines to the console (adding "\n" at the end of each line)
          $output->writeln([
              '',
              '********   Welcome! Setup Opencart ********',
              '===========================================',
              '',
              '',
          ]);
          $style = new OutputFormatterStyle('red');
          $output->getFormatter()->setStyle('error', $style);
          $style = new OutputFormatterStyle('green');
          $output->getFormatter()->setStyle('question', $style);

          $helper = $this->getHelper('question');

          $version_question     = new Question('<question>'.$this->data['text_version'].'</question>: ');
          $directory_question   = new Question("\n".'<question>'.$this->data['text_directory_creation'].'</question>: ');
          $dir_path_question    = new Question("\n\n".'<question>'.$this->data['text_directory_path'].'</question>: ');
          $dbhost_question      = new Question("\n\n".'<question>'.$this->data['text_enter_hostname'].'</question>: ');
          $dbuser_question      = new Question("\n\n".'<question>'.$this->data['text_enter_username'].'</question>: ');
          $dbpass_question      = new Question("\n\n".'<question>'.$this->data['text_enter_password'].'</question>: ');
          $dbname_question      = new Question("\n\n".'<question>'.$this->data['text_enter_dbname'].'</question>: ');
          $dbport_question      = new Question("\n\n".'<question>'.$this->data['text_enter_dbport'].'</question>: ');
          $dbprefix_question    = new Question("\n\n".'<question>'.$this->data['text_enter_prefix'].'</question>: ');
          $adminuser_question   = new Question("\n\n".'<question>'.$this->data['text_admin_username'].'</question>: ');
          $adminpass_question   = new Question("\n\n".'<question>'.$this->data['text_admin_password'].'</question>: ');
          $adminemail_question  = new Question("\n\n".'<question>'.$this->data['text_admin_email'].'</question>: ');
          $http_server_question = new Question("\n\n".'<question>'.$this->data['text_http_server'].'</question>: ');


          $this->inputData['oc_version'] = $helper->ask($input, $output, $version_question);
          if($this->inputData['oc_version'] != ''){

              //function call to validate the entered opencart version
              while (!$this->validateOpencartVersion($this->inputData['oc_version'])) {
                  $output->writeln("\n<error>".sprintf($this->data['error_invalid_version_provided'], $this->inputData['oc_version'])."</error>\n");
                  sleep(3);
                      $this->inputData['oc_version'] = $helper->ask($input, $output, $version_question);
              }

              $this->inputData['oc_directory'] = $helper->ask($input, $output, $directory_question);

              //function call to validate the entered answer for creating opencart directory
              while (!$this->validateDirectoryAnswer($this->inputData['oc_directory'])) {
                $output->writeln("\n<error>".sprintf($this->data['error_wrong_answer'], $this->inputData['oc_version'])."</error>\n");
                sleep(3);
                  $this->inputData['oc_directory'] = $helper->ask($input, $output, $directory_question);
              }

              if($this->inputData['oc_directory'] == 'y' || $this->inputData['oc_directory'] == 'Y'){

                  $this->inputData['directory_path'] = $helper->ask($input, $output, $dir_path_question);

                  while (!$this->validateNcreateDirectoryPath($this->inputData['directory_path'])){
                    $output->writeln("\n<error>".$this->data['error_dir_path']."</error>\n");
                    sleep(3);
                        $this->inputData['directory_path'] = $helper->ask($input, $output, $dir_path_question);
                  }

                  //function to download the opencart zip from github and extract that zip
                  while (!$this->downloadOpencartZip($this->newDirPath)){
                    $output->writeln("\n<error>".$this->data['error_dir_path']."</error>\n");
                    sleep(3);
                        $this->inputData['directory_path'] = $helper->ask($input, $output, $dir_path_question);
                  }

              }else if($this->inputData['oc_directory'] == 'n' || $this->inputData['oc_directory'] == 'N'){
                  //function to download the opencart zip from github and extract that zip
                  while (!$this->downloadOpencartZip($this->newDirPath)){
                    $output->writeln("\n<error>".$this->data['error_dir_path']."</error>\n");
                    sleep(3);
                        $this->inputData['directory_path'] = $helper->ask($input, $output, $dir_path_question);
                  }
              }
              if(!file_exists($this->newDirPath.'/system/storage/upload')){
                mkdir($this->newDirPath.'/system/storage/upload', 0755, TRUE);
              }

              $file=fopen($this->newDirPath.'/system/startup.php',"r+") or exit("Unable to open file!");
              $newline = '';
              $newuser  = '$_SERVER[\'SERVER_PORT\'] = false;'."\n";
              $insertPos=0;  // variable for saving //Users position
              while (!feof($file)) {
                  $line=fgets($file);
                  if (strpos($line, '// Check Version')!==false) {
                      $insertPos=ftell($file);    // ftell will tell the position where the pointer moved, here is the new line after //Users.
                      $newline =  $newuser;
                  } else {
                      $newline.=$line;   // append existing data with new data of user
                  }
              }
              fseek($file,$insertPos);   // move pointer to the file position where we saved above
              fwrite($file, $newline);
              fclose($file);


              // outputs multiple lines to the console (adding "\n" at the end of each line)
              $output->writeln([
                  '',
                  '********   Enter Database Details ********',
                  '===========================================',
                  '',
                  '',
              ]);

              $this->inputData['db_hostname']   = $helper->ask($input, $output, $dbhost_question);
              $this->inputData['db_username']   = $helper->ask($input, $output, $dbuser_question);
              $this->inputData['db_password']   = $helper->ask($input, $output, $dbpass_question);
              $this->inputData['db_database']   = $helper->ask($input, $output, $dbname_question);
              $this->inputData['db_port']       = $helper->ask($input, $output, $dbport_question);
              $this->inputData['db_prefix']     = $helper->ask($input, $output, $dbprefix_question);
              $this->inputData['username']      = $helper->ask($input, $output, $adminuser_question);
              $this->inputData['password']      = $helper->ask($input, $output, $adminpass_question);
              $this->inputData['email']         = $helper->ask($input, $output, $adminemail_question);
              $this->inputData['http_server']   = $helper->ask($input, $output, $http_server_question);
              $valid = array();
              $valid = $this->valid($this->inputData);
              while(!$valid[0]){
                  $output->writeln("\n<error>FAILED! Following inputs were missing or invalid: ".implode(', ', $valid[1])."</error>\n\n");
                  sleep(5);
                  $this->inputData['db_hostname']   = $helper->ask($input, $output, $dbhost_question);
                  $this->inputData['db_username']   = $helper->ask($input, $output, $dbuser_question);
                  $this->inputData['db_password']   = $helper->ask($input, $output, $dbpass_question);
                  $this->inputData['db_database']   = $helper->ask($input, $output, $dbname_question);
                  $this->inputData['db_port']       = $helper->ask($input, $output, $dbport_question);
                  $this->inputData['db_prefix']     = $helper->ask($input, $output, $dbprefix_question);
                  $this->inputData['username']      = $helper->ask($input, $output, $adminuser_question);
                  $this->inputData['password']      = $helper->ask($input, $output, $adminpass_question);
                  $this->inputData['email']         = $helper->ask($input, $output, $adminemail_question);
                  $this->inputData['http_server']   = $helper->ask($input, $output, $http_server_question);
              }

              $command   = 'php '.$this->newDirPath.'/install/cli_install.php install'.' --db_hostname '.$this->inputData['db_hostname'].' --db_username '.$this->inputData['db_username'].' --db_password '. $this->inputData['db_password'].' --db_database '.$this->inputData['db_database'].' --db_driver mysqli --db_port '.$this->inputData['db_port'].' --db_prefix '.$this->inputData['db_prefix'].' --username '.$this->inputData['username'].' --password '.$this->inputData['password'].' --email '.$this->inputData['email'].' --http_server '.$this->inputData['http_server'];

              echo shell_exec($command);
          }else{
              $output->writeln("\n<error>".$this->data['error_invalid_version']."</error>\n");
              sleep(5);
                  $this->inputData['oc_version'] = $helper->ask($input, $output, $version_question);
          }
      }

      function valid($options) {
      	$required = array(
      		'db_hostname',
      		'db_username',
      		'db_password',
      		'db_database',
      		'db_prefix',
      		'db_port',
      		'username',
      		'password',
      		'email',
      		'http_server',
      	);
      	$missing = array();
      	foreach ($required as $r) {
      		if (!array_key_exists($r, $options)) {
      			$missing[] = $r;
      		}
      	}
      	if (!preg_match('#/$#', $options['http_server'])) {
      		$options['http_server'] = $options['http_server'] . '/';
      	}
      	$valid = count($missing) === 0;
      	return array($valid, $missing);
      }

      // function to validate the entered opencart version
      public function validateOpencartVersion($opencart_version = false){
          if($opencart_version){
              if(in_array(trim($opencart_version), $this->opencartVersion)){
                return true;
              }
          }else{
            return false;
          }
      }

      // function to validate the entered answer for opencart directory
      public function validateDirectoryAnswer($ocDirectoryAnswer = false){
          if($ocDirectoryAnswer){
              if(in_array(trim($ocDirectoryAnswer), $this->confirmAnswer)){
                return true;
              }
          }else{
            return false;
          }
      }

      // function to validate and create the entered directory path to setup opencart
      public function validateNcreateDirectoryPath($directoryPath = false){
          if($directoryPath){
              if (preg_match("/^[a-zA-z0-9\.\/]+$/", $directoryPath) && ((strlen(trim($directoryPath)) > 3) || (strlen(trim($directoryPath)) < 20))){

                  $directoryArray = array();
                  $directoryArray = explode("/", $directoryPath);
                  $current_directory_path = getcwd();
                  foreach (array_filter($directoryArray) as $key => $directory) {
                      if($directory == '..'){
                        $getCurrentPathArray = explode("/", $current_directory_path);
                        array_pop($getCurrentPathArray);
                        $current_directory_path = implode("/", $getCurrentPathArray);
                      }else if(!file_exists($current_directory_path.'/'.$directory)){
                        mkdir($current_directory_path.'/'.$directory);
                        chmod($current_directory_path.'/'.$directory, 0775);
                        $current_directory_path = $current_directory_path.'/'.$directory;
                      }else if(file_exists($current_directory_path.'/'.$directory)){
                          $current_directory_path = $current_directory_path.'/'.$directory;
                      }
                  }

                  $this->newDirPath = $current_directory_path;
                return true;
              }else{
                return false;
              }
          }else{
            return false;
          }
      }



      public function downloadOpencartZip($directoryPath = false){
          $currentDirectoryPath = getcwd();
          $status_move = false;
          if(isset($this->inputData['oc_version']) && in_array(trim($this->inputData['oc_version']), $this->opencartVersion)){
            if($directoryPath == ''){
                $directoryPath = $currentDirectoryPath;
            }
            $source_path      = $currentDirectoryPath.'/'.$this->inputData['oc_version'].'.zip';
            $destination_path = $directoryPath.'/'.$this->inputData['oc_version'].'.zip';
            $extract_path     = $directoryPath.'/';

            $output = '';
            $output = shell_exec($currentDirectoryPath.'/app/install_oc.sh '.$this->inputData['oc_version']);

            if((int)$output){
                if(file_exists($source_path)){
                    if (copy($source_path, $destination_path)) {

                        $versionZIP = new \ZipArchive;
                        if ($versionZIP->open($destination_path) === TRUE) {
                            $versionZIP->extractTo($extract_path);
                            $versionZIP->close();
                            $status_move = $this->moveDirectory($source_path, $destination_path, $extract_path);

                        }
                    }
                }
            }
        }
        return $status_move;
    }

      public function moveDirectory($source, $destination, $extract_path){
          $currentDirectoryPath = getcwd();
          $setupRootDirectory = $extract_path.'opencart-'.$this->inputData['oc_version'];
          // Get a list of files ready to upload
    			$files = array();

    			$path = array($setupRootDirectory.'/upload/' . '*');

    			while (count($path) != 0) {
      				$next = array_shift($path);

      				foreach ((array)glob($next) as $file) {
        					if (is_dir($file)) {
                    $getRemainPath = explode('opencart-'.$this->inputData['oc_version'].'/upload', $file);
                    if(isset($getRemainPath[1]) && $getRemainPath[1]){
                      if(!is_dir($extract_path.$getRemainPath[1])){
                          mkdir($extract_path.$getRemainPath[1], 0775, TRUE);
                      }
                    }
        						$path[] = $file . '/*';
        					}

                  // Add the file to the files to be deleted array
          				if(is_file($file)){
                      $getRemainPath = explode('opencart-'.$this->inputData['oc_version'].'/upload', $file);

                      if(isset($getRemainPath[1]) && $getRemainPath[1]){
                          if($getRemainPath[1] == '/config-dist.php'){
                            rename($file, $extract_path.'config.php');
                          }else if($getRemainPath[1] == '/admin/config-dist.php'){
                            rename($file, $extract_path.'admin/config.php');
                          }else{
                              if(!file_exists($extract_path.$getRemainPath[1])){
                                  copy($file, $extract_path.$getRemainPath[1]);
                              }
                          }
                      }
                  }
      				}
    			}

        unlink($source);
        unlink($destination);
        return true;
      }
}
