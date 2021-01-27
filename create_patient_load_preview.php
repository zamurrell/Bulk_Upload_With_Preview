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

function preview_spreadsheet_data($destination)
{

    $filename = $destination;
    $inputFileType = PHPExcel_IOFactory::identify($filename);
    $reader = PHPExcel_IOFactory::createReader($inputFileType);

    $spreadsheet = $reader->load($filename);

    $num_rows = $spreadsheet->getActiveSheet()->getHighestDataRow();

    $num_rows_display = $num_rows - 1;

    echo "Number of data rows: " . $num_rows_display . "\n";
    // print("File:" . $destination . '<br>');
    $objPHPExcel = PHPExcel_IOFactory::load($destination);

    $columns = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q");
    $html_string = "";

    foreach ($columns as $column) {
        $cell = $column . "1";
        $value = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $value_lowercase_trimmed = trim(strtolower($value));
        $detected_known_value = false;
        // echo $column . "1: " . $value . "<br>";

        if ($value != "") {
            // if ($value_lowercase_trimmed == "clinic id") {
            //     $clinic_id_col = $column;
            //     $detected_known_value = true;
            // } else 
            if ($value_lowercase_trimmed == "clinic patient id") {
                $clinic_patient_id_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "clinic name") {
                $clinic_name_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "active") {
                $active_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "first name") {
                $first_name_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "last name") {
                $last_name_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "dob" || $value_lowercase_trimmed == "date of birth") {
                $dob_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "gender") {
                $gender_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "address") {
                $address_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "city") {
                $city_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "state") {
                $state_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "zip" || $value_lowercase_trimmed == "zipcode" || $value_lowercase_trimmed == "zip code") {
                $zip_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "primary insurance") {
                $primary_insurance_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "policy #" || $value_lowercase_trimmed == "policy no" || $value_lowercase_trimmed == "policy num" || $value_lowercase_trimmed == "policy number" || $value_lowercase_trimmed == "policy num." || $value_lowercase_trimmed == "policy no.") {
                $policy_number_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "email address" || $value_lowercase_trimmed == "email") {
                $email_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "phone" || $value_lowercase_trimmed == "phone #" || $value_lowercase_trimmed == "phone no" || $value_lowercase_trimmed == "phone num" || $value_lowercase_trimmed == "phone number" || $value_lowercase_trimmed == "phone num." || $value_lowercase_trimmed == "phone no." || $value_lowercase_trimmed == "mobile") {
                $phone_col = $column;
                $detected_known_value = true;
            } else if ($value_lowercase_trimmed == "policy holder/guarantor" || $value_lowercase_trimmed == "policy holder" || $value_lowercase_trimmed == "policy guarantor" || $value_lowercase_trimmed == "holder/guarantor" || $value_lowercase_trimmed == "holder" || $value_lowercase_trimmed == "guarantor" || $value_lowercase_trimmed == "policy guarantor/holder" || $value_lowercase_trimmed == "guarantor/holder") {
                $policy_holder_col = $column;
                $detected_known_value = true;
            }

            if ($detected_known_value === true) {
            } else {
                $html_string .= "<span style='color:red;'>" . $value . " - column not recognized</span><br>";
            }
        }
    }
    $html_string .= "<br>Preview of detected data in spreadsheet shown below. Click green button to proceed with creating patients<br><br><button id='after_review_button' type='button' class='btn btn-success' style='margin:10px;'>Create Patients</button><button id='cancel_button' type='button' class='btn btn-dark' style='margin:10px;'>Cancel</button><br><br>";
    $chosen_client = $_POST['client_id'];
    $get_client_name_sql = "select name from client where id='" . $chosen_client . "'";
    $client_name_from_query = $GLOBALS['ahi_pdo']->query($get_client_name_sql)->get_all();
    // $html_string .= "<h3>Chosen client: " . $client_name_from_query[0]['name'] . "</h3>";

    $html_string .= "<table><tr><th>Clinic Patient ID</th><th>First Name</th><th>Last Name</th><th>Date of Birth</th><th>Gender</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>Primary Insurance</th><th>Policy Number</th><th>Email</th><th>Phone</th><th>Policy Holder</th></tr>";

    $startrow = 2;
    $maxrows = 15000;
    $zi = 0;
    $InvDate = "Start";
    $failed_insert = array();
    $num_rows_data = $num_rows - 1;

    // while ($InvDate <> '' && $zi < $maxrows) {
    for ($x = 0; $x < $num_rows_data; $x++) {
        ++$zi;
        $failed = false;
        $note = "";
        //set_time_limit(360);


        // $cell = $clinic_id_col . $startrow;
        // $clinic_id = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        // if ($clinic_id != "") {
        //     $clinic_id_cleaned = trim($clinic_id);
        //     $check_client_sql = "Select * from client where id='" . $clinic_id_cleaned . "'";
        //     $already_exists_client = $GLOBALS['ahi_pdo']->query($check_client_sql)->get_all();
        //     $number = count($already_exists_client);
        //     if ($number == 0) {
        //         // $failed = true;
        //         // $note .= "Client doesn't exist.";
        //         $html_string .= "<td><span style='color:red;'>" . $clinic_id . " is not an existing client</span></td>";
        //     } else {
        //         $html_string .= "<td>" . $clinic_id . "</td>";
        //     }
        // } else {
        //     //   $failed = true;
        //     //   $note .= "Client ID error.";
        //     $html_string .= "<td><span style='color:red;'>{ Empty Cell - Clinic/Client ID Required }</span></td>";
        // }


        $cell = $clinic_patient_id_col . $startrow;
        $clinic_patient_id = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($clinic_patient_id != "") {
            $html_string .= "<td>" . $clinic_patient_id . "</td>";
        } else {
            $html_string .= "<td></td>";
        }

        $cell = $first_name_col . $startrow;
        $first_name = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $last_name_col . $startrow;
        $last_name = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();

        if ($last_name != "" && $first_name != "") {
            $first_name_search = $first_name;
            $last_name_search = $last_name;
        }

        if ($first_name != "") {
            $html_string .= "<td>" . $first_name . "</td>";
        } else {
            $html_string .= "<td><span style='color:red;'>{ First Name Required }</span></td>";
        }
        if ($last_name != "") {
            $html_string .= "<td>" . $last_name . "</td>";
        } else {
            $html_string .= "<td><span style='color:red;'>{ Last Name Required }</span></td>";
        }
        $cell_index = $dob_col . $startrow;
        $cell = $objPHPExcel->getActiveSheet()->getCell($cell_index);
        $InvDate = $cell->getValue();
        if (PHPExcel_Shared_Date::isDateTime($cell)) {
            if ($InvDate != "") {
                $InvDate = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($InvDate));
                $html_string .= "<td>" . $InvDate . "</td>";
            } else {
                $html_string .= "<td><span style='color:red;'>{ Date of Birth Required }</span></td>";
            }
        } else {
            $html_string .= "<td><span style='color:red;'>{ " . $InvDate . " not formatted as date - Error }</span></td>";
        }
        // $dob = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();

        // echo "DOB: " . $InvDate . "<br>"; // debug
        // echo "DOB exploded: " . json_encode($dob_exploded) . "<br>"; // debug

        $cell = $gender_col . $startrow;
        $gender = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $gender_cleaned = trim(strtolower($gender));
        if ($gender != "") {
            if ($gender_cleaned == "f" || $gender_cleaned == "female" || $gender_cleaned == "2" || $gender_cleaned == 2) {
                $gender_insert = "Female";
                $html_string .= "<td>" . $gender_insert . "</td>";
            } else if ($gender_cleaned == "m" || $gender_cleaned == "male" || $gender_cleaned == "3" || $gender_cleaned == 3) {
                $gender_insert = "Male";
                $html_string .= "<td>" . $gender_insert . "</td>";
            } else {
                $gender_insert = "";
                $html_string .= "<td><span style='color:red;'>{ Unrecognized gender }</span></td>";
            }
        } else {
            $html_string .= "<td><span style='color:red;'>{ Gender Required }</span></td>";
        }

        $cell = $address_col . $startrow;
        $address = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $city_col . $startrow;
        $city = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $state_col . $startrow;
        $state = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $cell = $zip_col . $startrow;
        $zip = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $html_string .= "<td>" . $address . "</td>";
        $html_string .= "<td>" . $city . "</td>";
        $html_string .= "<td>" . $state . "</td>";
        $html_string .= "<td>" . $zip . "</td>";
        $cell = $primary_insurance_col . $startrow;
        $primary_insurance = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($primary_insurance != "") {
            $html_string .= "<td>" . $primary_insurance . "</td>";
        } else {
            $html_string .= "<td><span style='color:red;'>{ Primary Insurance Required }</span></td>";
        }
        $cell = $policy_number_col . $startrow;
        $policy_number = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($policy_number != "") {
            $html_string .= "<td>" . $policy_number . "</td>";
        } else {
            $html_string .= "<td><span style='color:red;'>{ Policy Number Required }</span></td>";
        }
        $cell = $email_col . $startrow;
        $email = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $html_string .= "<td>" . $email . "</td>";
        $cell = $phone_col . $startrow;
        $phone = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        if ($phone != "") {
            $phone_trimmed = trim($phone);
            $string = str_replace('-', '', $phone_trimmed);
            $phone_cleaned = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
            $html_string .= "<td>" . $phone_cleaned . "</td>";
        } else {
            $html_string .= "<td></td>";
        }
        $cell = $policy_holder_col . $startrow;
        $policy_holder = $objPHPExcel->setActiveSheetIndex(0)->getCell($cell)->getFormattedValue();
        $html_string .= "<td>" . $policy_holder . "</td>";

        // $insert_sql = substr($insert_sql, 0, (strlen($insert_sql) - 1));

        if (isset($first_name_search) && isset($last_name_search)) {
            $find_if_already_exists_sql = "select * from patient where first_name='" . $first_name_search . "' and last_name='" . $last_name_search . "' and date_of_birth='" . $InvDate . "' and client_id='" . $chosen_client . "'";
            $already_exists = $GLOBALS['ahi_pdo']->query($find_if_already_exists_sql)->get_all();
            $number = count($already_exists);
            if ($number > 0) {
                $failed = true;
                $failed_insert[] = $first_name . " " . $last_name . " birthdate: " . $InvDate . " is already a patient with this client. Patient ID: " . $already_exists[0]['id'] . ".";
            }
        }

        $html_string .= "</tr>";

        ++$startrow;
    }
    $html_string .= "<br><br>";
    foreach ($failed_insert as $failed) {
        $html_string .= $failed . "<br>";
    }
    $html_string .= "<br><br>";
    echo $html_string;
}

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
        $test = preview_spreadsheet_data($source_filepath);
    }
}
