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
            $data = $form->getData();
            $file = $request->files->get('document')["attachment"];
            
            $uploads_directory = $this->getParameter('uploads_directory');

            $fileOrignalName = $request->files->get('document')['attachment']->getClientOriginalName();
            $addDate = $request->files->get('document')['attachment']->getcTime();

            dump($addDate);

//            $file->move(
//                $uploads_directory,
//                $fileOrignalName
//            );
        }

        return $this->render('document/add.html.twig', [
            'pagename' => 'Add document',
            'form' => $form->createView()
        ]);
    }
}
