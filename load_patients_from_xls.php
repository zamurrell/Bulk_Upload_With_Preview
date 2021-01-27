<?php

/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.8.0, 2014-03-02
 */
set_time_limit(600);
/** Error reporting */
include('fpcaredb.php');
include_once('ahi_pdo.php');
if (class_exists('lisDB') and !isset($GLOBALS['ahi_pdo'])) {
    $GLOBALS['ahi_pdo'] = new lisDB();
}

$filename = $_FILES['upload_input']['name'];

$nowdate = date("Y-m-d");
define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

function load_patients($destination)
{
    // print("File:" . $destination . '<br>');
    $filename = $destination;
    $inputFileType = PHPExcel_IOFactory::identify($filename);
    $reader = PHPExcel_IOFactory::createReader($inputFileType);

    $spreadsheet = $reader->load($filename);

    $num_rows = $spreadsheet->getActiveSheet()->getHighestDataRow();

    $num_rows_display = $num_rows - 1;

    echo "Number of data rows: " . $num_rows_display . "\n";

    $objPHPExcel = PHPExcel_IOFactory::load($destination);

    $columns = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q");
    $successful_creations = 0;
    $failed_creations = 0;

    print("<button id='refresh_button' type='button' class='btn btn-success' style='margin:10px;'>Upload another spreadsheet</button><br><br>");

    $chosen_client = $_POST['client_id'];

    foreach ($columns as $column) {
        $cell = $column . "1";
        $value = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $value_lowercase_trimmed = trim(strtolower($value));
        // echo $column . "1: " . $value . "<br>";

        // if ($value_lowercase_trimmed == "clinic id") {
        //   $clinic_id_col = $column;
        // } else 
        if ($value_lowercase_trimmed == "clinic patient id") {
            $clinic_patient_id_col = $column;
        } else if ($value_lowercase_trimmed == "clinic name") {
            $clinic_name_col = $column;
        } else if ($value_lowercase_trimmed == "active") {
            $active_col = $column;
        } else if ($value_lowercase_trimmed == "first name") {
            $first_name_col = $column;
        } else if ($value_lowercase_trimmed == "last name") {
            $last_name_col = $column;
        } else if ($value_lowercase_trimmed == "dob" || $value_lowercase_trimmed == "date of birth") {
            $dob_col = $column;
        } else if ($value_lowercase_trimmed == "gender") {
            $gender_col = $column;
        } else if ($value_lowercase_trimmed == "address") {
            $address_col = $column;
        } else if ($value_lowercase_trimmed == "city") {
            $city_col = $column;
        } else if ($value_lowercase_trimmed == "state") {
            $state_col = $column;
        } else if ($value_lowercase_trimmed == "zip" || $value_lowercase_trimmed == "zipcode" || $value_lowercase_trimmed == "zip code") {
            $zip_col = $column;
        } else if ($value_lowercase_trimmed == "primary insurance") {
            $primary_insurance_col = $column;
        } else if ($value_lowercase_trimmed == "policy #" || $value_lowercase_trimmed == "policy no" || $value_lowercase_trimmed == "policy num" || $value_lowercase_trimmed == "policy number" || $value_lowercase_trimmed == "policy num." || $value_lowercase_trimmed == "policy no.") {
            $policy_number_col = $column;
        } else if ($value_lowercase_trimmed == "email address" || $value_lowercase_trimmed == "email") {
            $email_col = $column;
        } else if ($value_lowercase_trimmed == "phone" || $value_lowercase_trimmed == "phone #" || $value_lowercase_trimmed == "phone no" || $value_lowercase_trimmed == "phone num" || $value_lowercase_trimmed == "phone number" || $value_lowercase_trimmed == "phone num." || $value_lowercase_trimmed == "phone no." || $value_lowercase_trimmed == "mobile") {
            $phone_col = $column;
        } else if ($value_lowercase_trimmed == "policy holder/guarantor" || $value_lowercase_trimmed == "policy holder" || $value_lowercase_trimmed == "policy guarantor" || $value_lowercase_trimmed == "holder/guarantor" || $value_lowercase_trimmed == "holder" || $value_lowercase_trimmed == "guarantor" || $value_lowercase_trimmed == "policy guarantor/holder" || $value_lowercase_trimmed == "guarantor/holder") {
            $policy_holder_col = $column;
        }
    }

    // echo "<br><br>clinic_id_col: " . $clinic_id_col . "<br>";
    // echo "clinic_patient_id_col: " . $clinic_patient_id_col . "<br>";
    // echo "clinic_name_col: " . $clinic_name_col . "<br>";
    // echo "active_col: " . $active_col . "<br>";
    // echo "first_name_col: " . $first_name_col . "<br>";
    // echo "last_name_col: " . $last_name_col . "<br>";
    // echo "dob_col: " . $dob_col . "<br>";
    // echo "gender_col: " . $gender_col . "<br>";
    // echo "address_col: " . $address_col . "<br>";
    // echo "city_col: " . $city_col . "<br>";
    // echo "state_col: " . $state_col . "<br>";
    // echo "zip_col: " . $zip_col . "<br>";
    // echo "primary_insurance_col: " . $primary_insurance_col . "<br>";
    // echo "policy_number_col: " . $policy_number_col . "<br>";
    // echo "email_col: " . $email_col . "<br>";
    // echo "phone_col: " . $phone_col . "<br>";
    // echo "policy_holder_col: " . $policy_holder_col . "<br>";

    $startrow = 2;
    $maxrows = 15000;
    $zi = 0;
    $InvDate = "Start";
    $failed_insert = array();
    $num_rows_data = $num_rows - 1;

    // while ($InvDate <> '' && $zi < $maxrows) {
    // while ($InvDate <> '' && $zi < $maxrows) {
    for ($x = 0; $x < $num_rows_data; $x++) {
        ++$zi;
        $failed = false;
        $note = "";
        //set_time_limit(360);

        $insert_sql = "insert into patient set";

        // $cell = $clinic_id_col . $startrow;
        // $clinic_id = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        // if ($clinic_id != "") {
        //   $clinic_id_cleaned = trim($clinic_id);
        //   $check_client_sql = "Select * from client where id='" . $clinic_id_cleaned . "'";
        //   $already_exists_client = $GLOBALS['ahi_pdo']->query($check_client_sql)->get_all();
        //   $number = count($already_exists_client);
        //   if ($number == 0) {
        //     $failed = true;
        //     $note .= "Client doesn't exist.";
        //   } else {
        //   }
        // } else {
        //   $failed = true;
        //   $note .= "Client ID error.";
        // }

        $insert_sql .= " client_id='" . $chosen_client . "',";

        $cell = $clinic_patient_id_col . $startrow;
        $clinic_patient_id = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($clinic_patient_id != "") {
            $clinic_patient_id_cleaned = trim($clinic_patient_id);
            $insert_sql .= " client_patient_id='" . $clinic_patient_id_cleaned . "',";
        }

        $cell = $first_name_col . $startrow;
        $first_name = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $last_name_col . $startrow;
        $last_name = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();

        if ($last_name != "" && $first_name != "") {
            $first_name_search = str_replace("'", "\'", $first_name);
            $last_name_search = str_replace("'", "\'", $last_name);
            $insert_sql .= " name='" . $first_name_search . " " . $last_name_search . "', first_name='" . $first_name_search . "', last_name='" . $last_name_search . "',";
        } else {
            $failed = true;
            $note .= "Name error.";
        }

        // } else if ($last_name != "") {
        //   $insert_sql .= " name='" . $last_name . "', first_name='', last_name='" . $last_name . "',";
        // } else if ($first_name != "") {
        //   $insert_sql .= " name='" . $first_name . "', first_name='" . $first_name . "', last_name='',";

        $cell_index = $dob_col . $startrow;
        $cell = $objPHPExcel->getActiveSheet()->getCell($cell_index);
        $InvDate = $cell->getValue();
        if (PHPExcel_Shared_Date::isDateTime($cell)) {
            if ($InvDate != "") {
                $InvDate = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($InvDate));
                $insert_sql .= " date_of_birth='" . $InvDate . "',";
            } else {
                $failed = true;
                $note .= "Empty DOB";
            }
        } else {
            $failed = true;
            $note .= "DOB Format Error";
        }
        // $dob = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();

        // echo "DOB: " . $InvDate . "<br>"; // debug
        // echo "DOB exploded: " . json_encode($dob_exploded) . "<br>"; // debug

        $cell = $gender_col . $startrow;
        $gender = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $gender_cleaned = trim(strtolower($gender));
        if ($gender != "") {
            if ($gender_cleaned == "f" || $gender_cleaned == "female" || $gender_cleaned == "2" || $gender_cleaned == 2) {
                $gender_insert = "2";
                $insert_sql .= " gender_id='" . $gender_insert . "',";
            } else if ($gender_cleaned == "m" || $gender_cleaned == "male" || $gender_cleaned == "3" || $gender_cleaned == 3) {
                $gender_insert = "3";
                $insert_sql .= " gender_id='" . $gender_insert . "',";
            } else {
                $failed = true;
                $note .= "Unrecognized gender";
            }
        } else {
            $failed = true;
            $note .= "Empty gender";
        }
        $cell = $address_col . $startrow;
        $address = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $city_col . $startrow;
        $city = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $state_col . $startrow;
        $state = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $zip_col . $startrow;
        $zip = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($address != "" && $city != "" && $state != "" && $zip != "" && strlen($zip) == 5) {
            $address_cleaned = trim($address);
            $address_cleaned = str_replace("'", "\'", $address_cleaned);
            $insert_sql .= " address1='" . $address_cleaned . "',";
            $city_cleaned = trim($city);
            $city_cleaned = str_replace("'", "\'", $city_cleaned);
            $insert_sql .= " city='" . $city_cleaned . "',";
            $state_cleaned = trim($state);
            $insert_sql .= " state='" . $state_cleaned . "',";
            $zip_cleaned = trim($zip);
            $insert_sql .= " zip_code='" . $zip_cleaned . "',";
        } else {
            $insert_sql .= " address1='1 No Address',";
            $insert_sql .= " city='Cumming',";
            $insert_sql .= " state='GA',";
            $insert_sql .= " zip_code='30041',";
        }
        $cell = $primary_insurance_col . $startrow;
        $primary_insurance = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($primary_insurance != "") {
            $primary_insurance_cleaned = trim($primary_insurance);
            $primary_insurance_cleaned = str_replace("'", "\'", $primary_insurance_cleaned);
            $insert_sql .= " primary_insurance_company='" . $primary_insurance_cleaned . "',";
        } else {
            $failed = true;
            $note .= "Primary Insurance Name error.";
        }
        $cell = $policy_number_col . $startrow;
        $policy_number = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($policy_number != "") {
            $policy_number_cleaned = trim($policy_number);
            $insert_sql .= " primary_insurance_policy_number='" . $policy_number_cleaned . "',";
        } else {
            $failed = true;
            $note .= "Primary Insurance Number error.";
        }
        $cell = $email_col . $startrow;
        $email = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($email != "") {
            $email_cleaned = trim($email);
            $insert_sql .= " email='" . $email_cleaned . "',";
        }
        $cell = $phone_col . $startrow;
        $phone = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($phone != "") {
            $phone_trimmed = trim($phone);
            $string = str_replace('-', '', $phone_trimmed);
            $phone_cleaned = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
            $insert_sql .= " phone='" . $phone_cleaned . "',";
        }
        $cell = $policy_holder_col . $startrow;
        $policy_holder = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($policy_holder != "") {
            $policy_holder_cleaned = trim($policy_holder);
            $policy_holder_cleaned = str_replace("'", "\'", $policy_holder_cleaned);
            $insert_sql .= " guarantor_name='" . $policy_holder_cleaned . "',";
        }

        $insert_sql .= " ethnicity_id='1',";

        $ssn = substr($first_name, 0, 1) . "." . $last_name . "_" . $InvDate;
        $insert_sql .= " ssn='" . $ssn . "',";

        $insert_sql = substr($insert_sql, 0, (strlen($insert_sql) - 1));

        if ($failed === false && isset($first_name_search) && isset($last_name_search)) {
            $find_if_already_exists_sql = "select * from patient where first_name='" . $first_name_search . "' and last_name='" . $last_name_search . "' and date_of_birth='" . $InvDate . "' and client_id='" . $chosen_client . "'";
            $already_exists = $GLOBALS['ahi_pdo']->query($find_if_already_exists_sql)->get_all();
            $number = count($already_exists);
            if ($number > 0) {
                $failed = true;
                $note .= "Patient already exists. Patient ID: " . $already_exists[0]['id'] . ".";
            }
        }

        // echo "Insert sql: " . $insert_sql . "<br>";
        ++$startrow;
        if ($failed === true) {
            $failed_insert[] = $first_name . " " .  $last_name . ": " . $InvDate . " " . $note;
            $failed_creations++;
        } else {
            $insert_patient = $GLOBALS['ahi_pdo']->query($insert_sql)->row_count();
            $successful_creations++;
        }
    }

    // echo "<br><br>Processed Data: " . "<br><br>";
    // foreach ($columns as $column) {
    //   $cell = $column . "1";
    //   $value = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
    //   echo $value . "\t";
    // }
    // $startrow = 2;
    // $maxrows = 15000;
    // $zi = 0;
    // $clinic_id = "Start";
    // while ($clinic_id <> '' && $zi < $maxrows) {
    //   ++$zi;
    //   foreach ($columns as $column) {
    //     $cell = $column . $startrow;
    //     $value = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
    //     echo $value . "\t";
    //   }
    //   ++$startrow;
    // }

    echo "Successfully created " . $successful_creations . " patients.<br>";
    echo "Failed to create " . $failed_creations . " patients:<br>";
    foreach ($failed_insert as $failed) {
        echo "<br>" . $failed;
    }
}

// $patients_inbound = '/home/NAS2/Zach/Inbound/';
$patients_archive = '/home/NAS2/Zach/Archive/';
// $files1 = scandir($patients_inbound);
// $i = 0;
// while ($i < sizeof($files1)) {
//   if (strpos($files1[$i], '.xls') > 0) {
//     print($files1[$i] . '<br>');
//     set_time_limit(600);
//     $test = load_patients($patients_inbound . $files1[$i]);
//     // rename($patients_inbound . $files1[$i], $patients_archive . $files1[$i]);
//   }
//   ++$i;
// }

if (isset($_FILES['upload_input'])) {
    $myFile = $_FILES['upload_input'];
    $allowed = array('xls', 'ods', 'xlsx');
    $myFile["name"] = preg_replace("/[^a-z0-9\_\-\.]/i", '', $myFile["name"]); // Remove blank spaces & irregular characters
    $ext = pathinfo($myFile["name"], PATHINFO_EXTENSION);
    if (!in_array($ext, $allowed)) {
        echo $myFile["name"] . " could not be uploaded, it must be an .xls, .xlsx, or .ods file.<br>";
        // exit;
    } else if ($myFile["error"] > 0) {
        echo "Error: " . $myFile["error"] . "<br>";
    } else {
        $source_filepath = $myFile["tmp_name"];
        set_time_limit(600);
        $test = load_patients($source_filepath);
    }
}
