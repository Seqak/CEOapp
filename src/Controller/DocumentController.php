<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/document", name="document.")
 */
class DocumentController extends AbstractController
{

    private function sendToDatabase($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

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

                $this->sendToDatabase($document);
            }
            else
            {
                $document->setAddDate();

                $this->sendToDatabase($document);
            }

            $this->addFlash('success', 'The document was added successfully');
            return $this->redirect($this->generateUrl('document.list'));
        }

        return $this->render('document/add.html.twig', [
            'pagename' => 'Add document',
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/list", name="list")
     */
    public function list(DocumentRepository $documentRepository)
    {
        $documents = $documentRepository->findBy(array(),
            array( 'addDate' => 'DESC')
        );

        return $this->render('document/list.html.twig', [
            'pagename' => 'Documents list',
            'documents' => $documents

        ]);
    }


    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit($id, Request $request, DocumentRepository $documentRepository, Filesystem $filesystem)
    {
        $document = $documentRepository->findOneBy(array(
            'id' => $id
        ));

        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $request->files->get('document')["attachment"];

            if ($file !== null) {

                $uploads_directory = $this->getParameter('uploads_directory');
                $extension = $request->files->get('document')['attachment']->guessExtension();

                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . "_" . random_int(1000, 9999) . '.' . $extension;

                $oldFilename = $document->getFileName();

                if ($oldFilename !== null)
                {$filesystem->remove($uploads_directory . '/' . $oldFilename);}

                $file->move( $uploads_directory, $filename );

                $document->setFileName($filename);

                $this->sendToDatabase($document);

                $this->addFlash('success', 'Document has been updated');
                return $this->redirect($this->generateUrl('document.list'));
            }
            else{

                $this->sendToDatabase($document);
            }
        }

        $filename = $form->getData()->getFileName();
        $documentId = $form->getData()->getId();

        return $this->render('document/edit.html.twig', [
            'pagename' => 'Edit document',
            'form' => $form->createView(),
            'filename' => $filename,
            'documentId' => $documentId,
        ]);
    }

    /**
     * @Route("/delete_file/document/{id}", name="delete_file")
     */
    public function deleteFile($id, DocumentRepository $documentRepository, Filesystem $filesystem)
    {
        $document = $documentRepository->findOneBy(array('id' => $id));
        $filename = $document->getFileName();
        $uploads_directory = $this->getParameter('uploads_directory');

        if ($filename !== null)
        {
            $filesystem->remove($uploads_directory . '/' . $filename);
            $document->setFileName(null);

            $this->sendToDatabase($document);
        }

        $this->addFlash('success', 'File has been deleted.');
        return $this->redirect($this->generateUrl('document.edit', [
            'id' => $id
        ]));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id, DocumentRepository $documentRepository, Filesystem $filesystem)
    {
        $document = $documentRepository->findOneBy(array(
            'id' => $id
        ));

        $filename = $document->getFileName();
        $uploads_directory = $this->getParameter('uploads_directory');

        if ($filename !== null)
        {
            $filesystem->remove($uploads_directory . '/' . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();
        }

        $this->addFlash('success', 'The document was deleted successfully');
        return $this->redirect($this->generateUrl('document.list'));
    }

}
