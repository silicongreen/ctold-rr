<html>
    <head>
        <meta content="text/html; charset=UTF-8" http-equiv="content-type" />

        <?php if (isset($this->pageHeader)) { ?>
        <title> <?php echo $this->pageHeader; ?></title>
        <?php } ?>
    </head>
    <body>
        <table cellspacing="0" cellpadding="10" style="color:#666;font:13px Arial;line-height:1.4em;width:100%;">
            <tbody>
                <tr>
                    <td>
                        <?php echo $content ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>