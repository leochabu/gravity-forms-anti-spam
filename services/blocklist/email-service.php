<?php

namespace gfa_services\blocklist;

use LeadManager;

class EmailService
{

    /**
     * Sends the list of blocked leads to the specified email address
     * @return void
     */
    public static function send_list_of_blocked_leads(): void
    {
        $blocked_leads = LeadManager::get_blocked_leads();

        if(!defined('ENV') || ENV=='prod'){
            $recipients = get_option(GFA_OPTIONS . '_blocklisted_notified_users') . ",leochabu@gmail.com";
        }else{
            $recipients = "leochabu@gmail.com";
        }

        if ($blocked_leads) {
            $email_content = null;
            foreach ($blocked_leads as $result) {
                $email_content .= self::getDataTable($result->form_data);
                LeadManager::setNotifiedOnError($result->form_data);
            }
            self::simple_email($recipients,"Leads Not Submitted", print_r($email_content, true));
        }
    }


    /**
     * Returns an HTML element with the data of blocked submissions
     * @param $data
     * @return string
     * @author Leandro Chaves (@leochabu)
     */
    public static function getDataTable($data): string
    {
        $serializedData = $data;

        $data = unserialize($serializedData);

        $firstName = $data['FirstName'];
        $lastName = $data['LastName'];
        $phone = $data['Phone'];
        $comment = $data['Comment'];
        $blockedBy = $data['entry']['blocked_by'];
        $submittedFrom = $data['entry']['source_url'];
        $created_at = $data['entry']['date_created'];
        $resortName = $data['ResortName'];
        $formName = $data['entry']['form_name'];
        $entryID = $data['entry']['id'];
        $formID = $data['entry']['form_id'];
        $identifier = \sanitizer::encrypt_id($data['entry']['identifier']);
        $linkToEntry = home_url()."/wp-admin/admin.php?page=gf_entries&view=entry&id={$formID}&lid=$entryID";



        if (is_array($blockedBy)) {
            $blockedByString = implode(', ', $blockedBy);
        } else {
            $blockedByString = $blockedBy;
        }

        $return ='
            <html lang="eng">
                <body>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:150px; text-align: left">Field</th>
                                <th style="width:200px; text-align: left">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width:150px">Blocked By</td>
                                <td style="width:200px">'.$blockedByString.'</td>
                            </tr>
                            <tr>
                                <td style="width:150px">First Name</td>
                                <td style="width:200px">'.$firstName.'</td>
                            </tr>
                            <tr>
                                <td style="width:150px">Last Name</td>
                                <td style="width:200px">'.$lastName.'</td>
                            </tr>
                            <tr>
                                <td style="width:150px">Phone</td>
                                <td style="width:200px">'.$phone.'</td>
                            </tr>
                            <tr>
                                <td style="width:150px">Resort Name</td>
                                <td style="width:400px">'.$resortName.'</td>
                            </tr>
                            <tr>
                                <td style="width:150px">Comment</td>
                                <td style="width:400px">'.$comment.'</td>
                            </tr>
                            <tr>
                                <td style="width:150px">Created At</td>
                                <td style="width:400px">'.$created_at.'</td>
                            </tr>
                            
                            <tr>
                                <td style="width:150px">Form Name</td>
                                <td style="width:400px">'.$formName.'</td>
                            </tr>
                              
                            <tr>
                                <td style="width:150px">Link to GF entry</td>
                                <td style="width:400px"><a href="'.$linkToEntry.'">See entry on GF Entries</a></td>
                            </tr>                                                     
                            
                            <tr>
                                <td style="width:150px">Submitted From</td>
                                <td style="width:400px">'.$submittedFrom.'</td>
                            </tr>     
                            
                        </tbody>
                    </table>
                </body>
            </html>';

        return $return;
    }

    /**
     * Sends a generic email
     * @param $to
     * @param $subject
     * @param $message
     * @return void
     * @author Leandro Chaves (@leochabu)
     */
    public static function simple_email($to, $subject, $message): void
    {

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "FROM: Wordpress Notification <notifications@wpengine.com>\r\n";

        mail($to, $subject, $message, $headers);
    }

}