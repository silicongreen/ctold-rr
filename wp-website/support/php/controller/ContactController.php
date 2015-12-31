<?php

class ContactController extends Controller
{
    public function contactAction()
    {
        $request    = $this->get('request');
        $validators = $this->get('model_validation');
        $template   = $this->get('template_interface');

        // Get the input

        $name     = $request->postVar('name');
        $mail     = $request->postVar('mail');
        $question = $request->postVar('question');
        $school = $request->postVar('school');
        $start_time = $request->postVar('start_time');
        $end_time = $request->postVar('end_time');
        $userInfo = $request->postVar('userInfo', false);

        // Validate the input

        $errors = $validators->validateContactData(compact('name', 'mail', 'question','school','start_time','end_time'));

        if(count($errors) === 0)
        {
            // Create the message

            $question = nl2br($question);
            $userInfo = json_decode($userInfo);

            $message = $template->renderView('email/contact.html.php', compact('mail', 'name', 'userInfo', 'question','school','start_time','end_time'), true);

            // Send the e-mail

            $to      = $this->get('config')->data['appSettings']['contactMail'];
            $subject = 'PHP Live Support Chat, question from ' . $name;

            $success = $this->get('mailer')->sendMessage($mail, $to, $subject, $message);

            // Return the response

            return $this->json(array('success' => $success));
        }

        // Return an error response

        return $this->json(array('success' => false, 'errors' => $errors));
    }
}

?>
