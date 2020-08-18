<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/document", name="document.")
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $request->files->get('document')["attachment"];

            if ($file !== null){

                $uploads_directory = $this->getParameter('uploads_directory');
                $extension = $request->files->get('document')['attachment']->guessExtension();

                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "_" . random_int(1000,9999) . '.' . $extension;

                $file->move(
                    $uploads_directory,
                    $filename
                );

                $document->setAddDate();
                $document->setFileName($filename);
                $document->setAddDate();

                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();
            }
            else
            {
                $document->setAddDate();

                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();
            }

            $this->addFlash('success', 'The document was added successfully');
//            return $this->redirect($this->generateUrl('path.to.show'));
        }

        return $this->render('document/add.html.twig', [
            'pagename' => 'Add document',
            'form' => $form->createView()
        ]);
    }
}
