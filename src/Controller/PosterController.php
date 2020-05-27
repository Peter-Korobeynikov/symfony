<?php

namespace App\Controller;

use App\Common\TSingleton;
use App\Entity\Poster;
use App\Form\PosterType;
use App\Repository\PosterRepository;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//require '../../vendor/autoload.php';

/**
 * @Route("/poster")
 */
class PosterController extends AbstractController
{
    use TSingleton;

    private $_mail = null;
    protected function init() {
        $this->_mail = new PHPMailer;
        $this->_mail->IsSMTP();                                    // telling the class to use SMTP
        $this->_mail->SMTPAuth   = true;                           // enable SMTP authentication
        $this->_mail->Port       = 465;                            // sets Port of SMTP server
        $this->_mail->Host       = "smtp.yandex.ru";                // sets the SMTP server
        $this->_mail->Username   = "korobeynikov.pv@yandex.ru";     // SMTP account username
        $this->_mail->Password   = "Esmart-1734";                   // SMTP account password
        $this->_mail->Priority   = 3;
        $this->_mail->Encoding   = '8bit';
        $this->_mail->ErrorInfo  = '';
        $this->_mail->CharSet    = 'UTF-8';
        $this->_mail->ContentType = 'text/html';
        $this->_mail->SMTPSecure = 'SSL';
    }
    public function __construct()   { $this->init(); }
    public function getMail()       { assert(isset($this->_mail)); return $this->_mail; }

    /**
     * @Route("/email", name="poster_new", methods={"GET","POST"})
     */
    public function sendEmail($manager, $entity, $act): Response {
        $mail = $this->getMail();
        $mail->isSendmail();  // Set PHPMailer to use the sendmail transport
        $mail->setFrom   ($_SERVER['MAIL_FROM'], 'Peter K.');  //Set who the message is to be sent from
        $mail->addReplyTo($_SERVER['MAIL_REPLY'], 'Peter K.'); //Set an alternative reply-to address
        $mail->addAddress($_SERVER['MAIL_TO'],    'John Doe'); //Set who the message is to be sent to

        // Само письмо ...
        $mail->Subject = 'PHPMailer sendmail test'; //Set the subject line
        $mail->msgHTML('<p>See Twig integration for better HTML integration!</p>', __DIR__);
       //$mail->msgHTML($this->renderView('poster/new.html.twig', ['param' => 'Привет!!!']),'text/html');
        $mail->AltBody = 'This is a plain-text message body'; //Replace the plain text body with one created manually
        $mail->addAttachment('images/logo.png'); //Attach an image file
        //send the message, check for errors
        if (!$mail->send()) {
            echo 'Mailer Error: '. $mail->ErrorInfo;
        } else {
            echo 'Message sent!';
        }
        return Response::create();
    }

    /**
     * @Route("/", name="poster_index", methods={"GET"})
     */
    public function index(PosterRepository $posterRepository): Response { return Response::create(); }

    /**
     * @Route("/{id}", name="poster_show", methods={"GET"})
     */
    public function show(Poster $poster): Response  { return Response::create(); }

    /**
     * @Route("/{id}/edit", name="poster_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Poster $poster): Response { return Response::create(); }

    /**
     * @Route("/{id}", name="poster_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Poster $poster): Response { return Response::create(); }
}
