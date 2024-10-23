<?php
    use gf_anti_spam\GFA_Settings;
    GFA_Settings::register_settings();
?>

<div class="wrap form-group">
    <h2>Lead Submission Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields( GFA_OPTIONS . '_options' ); ?>
        <?php do_settings_sections( GFA_OPTIONS . '_options' ); ?>
        <br>

        <h2><b>Blocklist settings</b></h2>
        <p><i>Please add the required terms separated by commas (,) in each box.</i>
            <br>
            <i>For email addresses, include full addresses with "@" and without spaces, e.g., "example@example.com".</i><br>
            <i>To exclude Top-Level Domains (TLDs), enter the TLD, e.g., "top.com".</i><br>
            <i>For phone numbers, do not add special characters, only numbers.</i>
        </p>

        <table style="text-align: left; width: 100%;">
            <tr >
                <th scope="row" style="text-align: left;padding-right: 15px"><label for="<?php echo GFA_OPTIONS . '_blocklist'; ?>">Blocked Terms</label></th>
                <td style="width: 100% text-align: left;">
                    <p><i>Include here the terms you want to have a match with the submission</i>
                        <br>
                        <i>Note that the match is per word. This means that each word in an input value compared with the terms in this list.</i><br>
                        <i>E.G: If the blacklist contains "lazy" and input value is "The quick brown fox jumps over the lazy dog.", the submission will be blocked.</i><br>
                    </p>
                    <label>
                        <textarea style="width: 70%;" rows="5" name="<?php echo GFA_OPTIONS . '_blocklist'; ?>" id="<?php echo GFA_OPTIONS . '_blocklist' ?>">
                            <?php echo get_option(GFA_OPTIONS . '_blocklist')?>
                        </textarea>
                    </label>
                </td>
            </tr>


        </table>



        <p style="border: 1px solid red; padding: 1em;">
            <b style="color:red">Important:</b> <br>
            Partial match is very sensitive. It means each individual word within a user's input is compared against the terms in this list.<br>
            This can trigger unexpected matches if a user's input contains a harmless word that's part of a blocked term.
            <br>
            For example:
            <br>
            "cryptocurrency" would be flagged if "crypto" is a blocked term.<br>
            "marketing team" would be flagged if "marketing" or "team" is a blocked term.<br>
            Use partial match with caution and consider the potential for these types of unintended consequences.
            <br>
        </p>


        <?php  submit_button(); ?>
    </form>
</div>