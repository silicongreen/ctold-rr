<div>
    
    <div class="header-previous-gk">
        <div class="f2">
            Previous Records
        </div>
    </div>
    
    <div class="answer-container f5">
        <div>
            <table>
                <thead>
                    <tr>
                        <td>Date</td>
                        <td>Question</td>
                        <td>Answer</td>
                    </tr>
                </thead>
                
                <tbody>
                    
                    <?php
                        $ar_ques = explode(',', $answers[0]->question);
                        $ar_answ = explode(',', $answers[0]->user_answer);
                    ?>
                    
                    <tr>
                        <td colspan="1" rowspan="<?php echo sizeof($ar_ques) + 1; ?>" style="vertical-align: middle;"><?php echo $answers[0]->date; ?></td>
                    </tr>
                    
                    <?php $i = 0; foreach ($ar_ques as $ques) { ?>
                    <tr>
                        <td style="color: #404040; font-size: 18px; padding: 20px; text-align: center; letter-spacing: 0.08em; "><?php echo $ques;?></td>
                        <td><?php echo $ar_answ[$i];?></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
                
            </table>
        </div>
        
        
        <div class="next-previous">
            
            <?php if ($has_previous) : ?>
            <div class="previous">
                <span>Previous</span>
            </div>
            <?php endif; ?>
            
            <?php if ($has_next) : ?>
            <div class="next">
                <span>Next</span>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
    
</div>

<style type="text/css">
    .header-previous-gk {
        background-color: #ABD373;
        border: none transparent;
        height: auto;
        min-height: 100px;
    }
    .header-previous-gk div {
        color: #ffffff;
        font-size: 60px;
        font-weight: bold;
        //padding-top: 45px;
        margin-left: auto; 
        margin-right: auto;
        text-align: center;
        text-shadow: 0px 2px 0px #334225;
        vertical-align: middle;
        white-space: nowrap;
        width: 30%;
        
    }
    .answer-container {
        background-color: #ffffff;
        padding: 60px 30px 100px;
    }
    .answer-container div {
        margin: auto;
    }
    .answer-container table {
        width: 100%;
    }
    .answer-container table th, td{
        border: none;
    }
    .answer-container table thead {
        font-size: 40px;
        color: #94979C;
        text-align: center;
    }
    .answer-container table thead td {
        padding-bottom: 40px;
        text-align: center;
    }
    .answer-container table tbody td {
        color: #404040;
        font-size: 18px;
        padding: 20px;
        text-align: center;
    }
    .answer-container table tr td:first-child {
        border-right: 1px solid #dddddd;
        color: #94979C;
    }
    .answer-container table tbody tr td:first-child {
        letter-spacing: 0.2em;
    }
    .answer-container table tr td:last-child {
        border-left: 1px solid #dddddd;
        color: #94979C;
    }
    .next-previous {
        margin: auto;
        padding-top: 20px;
        text-align: center;
        font-size: 22px;
        letter-spacing: 0.08em;
        width: 35%;
    }
    .next {
        background-color: #BFC3C6;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        padding: 5px 30px;
        color: #ffffff;
        cursor: pointer;
        float: right;
    }
    .previous {
        background-color: #BFC3C6;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        padding: 5px 30px;
        color: #ffffff;
        cursor: pointer;
        float: left;
    }
</style>