<?
$pagetitle = 'Bulk-Create Patients';

include("setuputils.php");
include('functions_list_test.php');
include('clientheader.php');
include('report_functions.php');

?>

<script>
    let heads = document.getElementsByTagName('head');
    let head;
    if (heads.length) head = heads[0];
    else {
        let html = document.getElementsByTagName('html');
        head = document.createElement('head');
        html.appendChild(head);
    }

    function sleep(milliseconds) {
        const date = Date.now();
        let currentDate = null;
        do {
            currentDate = Date.now();
        } while (currentDate - date < milliseconds);
    }

    // Additional Javascript and css scripts/links to append - not included in clientheader.php
    var scripts = {
        select2_js: {
            "type": "script",
            "media_type": "text/javascript",
            "source": "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"
        },
        materialize: {
            "type": "script",
            "media_type": "text/javascript",
            "source": "https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"
        },
        zach_css: {
            "type": "link",
            "media_type": "text/css",
            "source": "zachstests/report.css",
            "rel": "stylesheet"
        },
        select2_css: {
            "type": "link",
            "media_type": "text/css",
            "source": "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css",
            "rel": "stylesheet"
        },
        report_functions: {
            "type": "script",
            "media_type": "text/javascript",
            "source": "js/report_functions.js"
        },
    }

    var allScripts = [];

    //populate scripts array
    for (var el in scripts) {
        allScripts.push(scripts[el]);
    }

    var allScriptsLength = allScripts.length;

    // Insert scripts into DOM
    for (var i = 0; i < allScriptsLength; i++) {
        var element = document.createElement(allScripts[i]['type']);
        element.type = allScripts[i]['media_type'];
        // console.log("Type: ", allScripts[i]['type']);
        // console.log("Media type: ", element.type);
        if (element.type == "text/javascript") {
            element.src = allScripts[i]['source'];
            // console.log("Src/href: ", element.src);
        } else if (element.type == "text/css") {
            element.href = allScripts[i]['source'];
            // console.log("Src/href: ", element.href);
        }
        if (allScripts[i]['rel']) {
            element.rel = allScripts[i]['rel'];
            // console.log("Rel: ", element.rel);
        }
        head.appendChild(element);
    }

    window.onload = (e) => {
        jQuery('.search_text_field').select2();
    }

    jQuery("body").on("click", "#upload_button", function() {
        if ($("#patient_upload_form #client_id").val() == "") {
            alert("Please select Client");
        } else if ($("#upload_input").val() == '') {
            alert("Please select a file");
        } else {
            $('#ajax_output_html').html("<h3>Reading document...</h3>");
            console.log("Upload button clicked");
            let passForm = new FormData($('#patient_upload_form')[0]);

            $.ajax({
                // Your server script to process the upload
                // url: 'lockbox_lookup.php',
                url: 'create_patient_load_preview.php',
                type: 'POST',
                method: 'POST',

                async: false,

                // Form data
                data: passForm,

                // Tell jQuery not to process data or worry about content-type
                // You *must* include these options!
                cache: false,
                contentType: false,
                processData: false,

                // Custom XMLHttpRequest
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        // For handling the progress of the upload
                        myXhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                $('progress').attr({
                                    value: e.loaded,
                                    max: e.total,
                                });
                            }
                        }, false);
                    }
                    return myXhr;
                },

                error: function(jqXHR, textStatus, errorThrown) {
                    //alert(textStatus);
                    alert(errorThrown);
                    //alert('update_page.php');
                },

                success: function(return_data) {
                    // console.log(return_data);
                    // if (return_data != "") {
                    //     alert(return_data);
                    // }

                    sleep(500);
                    $('#ajax_output_html').html(return_data);
                    // window.location.reload();
                    // if (return_data == "Error, not an acceptable file type") {
                    //     alert("Error, not an acceptable file type, must be a pdf");
                    // }
                    // var form_action = "lockbox_labels_side_by_side_specific.php?pdfFilePath=" + return_data;
                    // document.getElementById("lockbox_label_split").action = form_action;

                    // $("#lockbox_label_split #file").val(return_data);
                    // $("#next_file_side_by_side").val(return_data);
                    // document.getElementById('next_file_side_by_side').value = return_data;
                    // console.log("Next file side by side val: ", $("#next_file_side_by_side").val());
                    // var data = JSON.parse(return_data);
                    // $('#resultswindow').html(return_data);
                    // console.log("Return data: ", return_data);
                }

            });
        }
    });

    jQuery("body").on("click", "#cancel_button", function() {
        window.location.reload();
    });

    jQuery("body").on("click", "#refresh_button", function() {
        window.location.reload();
    });

    jQuery("body").on("click", "#after_review_button", function() {
        if ($("#patient_upload_form #client_id").val() == "") {
            alert("Please select Client");
        } else if ($("#upload_input").val() == '') {
            alert("Please select a file");
        } else {
            $('#ajax_output_html').html("");
            console.log("After review button clicked");
            let passForm = new FormData($('#patient_upload_form')[0]);

            $.ajax({
                // Your server script to process the upload
                // url: 'lockbox_lookup.php',
                url: 'load_patients_from_xls.php',
                type: 'POST',
                method: 'POST',

                async: false,

                // Form data
                data: passForm,

                // Tell jQuery not to process data or worry about content-type
                // You *must* include these options!
                cache: false,
                contentType: false,
                processData: false,

                // Custom XMLHttpRequest
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        // For handling the progress of the upload
                        myXhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                $('progress').attr({
                                    value: e.loaded,
                                    max: e.total,
                                });
                            }
                        }, false);
                    }
                    return myXhr;
                },

                error: function(jqXHR, textStatus, errorThrown) {
                    //alert(textStatus);
                    alert(errorThrown);
                    //alert('update_page.php');
                },

                success: function(return_data) {
                    // console.log(return_data);
                    // if (return_data != "") {
                    //     alert(return_data);
                    // }

                    sleep(500);
                    $('#ajax_output_html').html(return_data);
                    // window.location.reload();
                    // if (return_data == "Error, not an acceptable file type") {
                    //     alert("Error, not an acceptable file type, must be a pdf");
                    // }
                    // var form_action = "lockbox_labels_side_by_side_specific.php?pdfFilePath=" + return_data;
                    // document.getElementById("lockbox_label_split").action = form_action;

                    // $("#lockbox_label_split #file").val(return_data);
                    // $("#next_file_side_by_side").val(return_data);
                    // document.getElementById('next_file_side_by_side').value = return_data;
                    // console.log("Next file side by side val: ", $("#next_file_side_by_side").val());
                    // var data = JSON.parse(return_data);
                    // $('#resultswindow').html(return_data);
                    // console.log("Return data: ", return_data);
                }

            });
        }
    });
</script>



<!-- Begin Payer html -->
<div class="main-container">
    <div class="atk-wrapper">
        <div id="lims-content-box" class="ui-widget-content atk-grid atk-box">
            <div id="lims-subcontent-box" class="atk-row">
                <form id="patient_upload_form" ENCTYPE="multipart/form-data">
                    <table cellpadding="3" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <b>Client:</b>
                            </td>
                            <td style='width:550px;'>
                                <select id="client_id" name="client_id" class="custom-dropdown__select custom-dropdown__select--emerald search_text_field">
                                    <option value="">Select a Client</option>
                                    <?
							$sql="select * from client where active='Y' and name <> '' order by name";
							//echo "$sql<br>";
							$result =mysql_query($sql);
							$number = mysql_num_rows($result);
							$i=0;
							while ( $i < $number ){
									$id = mysql_result($result,$i,"id");
									$name = mysql_result($result,$i,"name");
								print("<option value=\"$id\"");
								print(">$name</option>");

									++$i;
							}
					?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="upload_input"><b>Upload Document &nbsp; </b></label>
                                <input type="file" id="upload_input" name="upload_input" style="width: 400px; display: inline-block;" class="upload_patient_spreadsheet">
                            </td>
                            <!-- <div>
                <button type="button" id="upload_button" name="upload_button" class="btn btn-default btn-primary" style="width: 100%;  margin: 0px 10px; display: inline-block;">Upload</button>
            </div> -->
                        </tr>
                    </table>


                    <!-- <div id="resultswindow" style="height:300px;width:90%;border:1px solid #ccc;overflow:auto;padding:5px;"> -->
                    <div class="row">
                        <p style='padding-left:25px;'>Spreadsheet must have column titles in the first row, and no blank rows between data rows.</p>
                        <button id="upload_button" type="button" class="btn btn-info" style="margin:10px 10px 10px 300px;">Upload</button>
                    </div>
                </form>
                <div style="width:100%;" id='ajax_output_html'></div>
            </div>
        </div>
    </div>
</div>