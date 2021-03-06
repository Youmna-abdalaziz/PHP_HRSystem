<?php

/**
 * Created by PhpStorm.
 * User: El Mansy
 * Date: 2/1/2019
 * Time: 4:39 PM
 */

if(__ALLOW_ACCESS__ !=1) {
    header("location:../index.php");

}

class Register
{

    private $username;
    private $password;
    private $name;
    private $job;
    private $img;
    private $cv;
    private $file_name_img;
    private $file_name_cv;
    private $file_tmp_img;
    private $file_tmp_cv ;
    private $file_cv_dbname ;
    private $file_img_dbname ;
   private $errors = array();

    public function __construct($name,$username,$password,$job,$img,$cv)
    {
        $pass_length = strlen((string)$password);
        if ($pass_length > __PASSWORDMAXLENGTH__)
        {
            $this->errors[]='Too Long Password , Max Length is 16';
        }
        elseif ($pass_length < __PASSWORDMINLENGTH__)
        {
            $this->errors[]='Too Short Password , Min Length is 8';
        }

        if(empty($errors)==true)

        {
            $this->password = md5($password);
            $this->name=$name;
            $this->username=$username;
            $this->job=$job;
            $this->img=$img;
            $this->cv=$cv;

            $this->registration();
        }


    }

    public function registration ()
    {

//        $errors= array();
        $this->file_name_img = $this->img['name'];
        $file_size =$this->img['size'];
        $this->file_tmp_img =$this->img['tmp_name'];
        $file_type=$this->img['type'];
        $x=explode('.',$this->img['name']);
        $file_ext=strtolower(end($x));
        $expensions= array("jpg");
        if(in_array($file_ext,$expensions)=== false)
        {
            $this->errors[]="please choose a JPG ";
        }
        if($file_size > __MAXSIZE__)
        {
            $this->errors[]='File size must be excately 1 MB';
        }

        $this->file_name_img=  $this->username;
        $this->file_img_dbname=$this->file_name_img.".".$file_ext;

        $this->file_name_cv =  $this->cv['name'];
        $file_size2 =$this->cv['size'];
        $this->file_tmp_cv =$this->cv['tmp_name'];
        $file_type2=$this->cv['type'];
        $x2=explode('.',$this->cv['name']);
        $file_ext2=strtolower(end($x2));
        $expensions2= array("pdf");
        if(in_array($file_ext2,$expensions2)=== false)
        {
            $this->errors[]="please choose a pdf file.";
        }

        if($file_size2 > __MAXSIZE__)
        {
            $this->errors[]='File size must be excately 2 MB';
        }

        $this->file_name_cv =  $this->username;
        $this->file_cv_dbname=$this->file_name_cv.".".$file_ext2;





    }

    public function insert_form_data()
    {
        if(empty($this->errors)==true)
        {
            move_uploaded_file($this->file_tmp_img,"images/".$this->file_img_dbname);
            move_uploaded_file($this->file_tmp_cv,"images/cvs/".$this->file_cv_dbname);

            $dbhandler=new MYSQLHandler("users");
            $db_result=$dbhandler->insert_data($this->username,$this->password,$this->name,$this->job,$this->file_img_dbname,$this->file_cv_dbname);
            print_r($db_result);
            if ($db_result)
            {
                session_start();
                $_SESSION['username']=$this->username;
                $_SESSION["is_admin"] = false;
                header('location:../../index.php');
            }

        }

    }


    public function update_form_data($id)
    {
        if(empty($this->errors)==true)
        {
            move_uploaded_file($this->file_tmp_img,"views/public/images/".$this->file_img_dbname);
            move_uploaded_file($this->file_tmp_cv,"views/public/images/cvs/".$this->file_cv_dbname);

            $dbhandler=new MYSQLHandler("users");
            $db_result=$dbhandler->update_data($id,$this->name,$this->username,$this->password,$this->job,$this->file_img_dbname,$this->file_cv_dbname);
            if ($db_result)
            {
                $_SESSION['username']=$this->username;

                header('location:index.php');
            }
        }

    }

    public function getErrors()
    {
        return $this->errors;
    }
}