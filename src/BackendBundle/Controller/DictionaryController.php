<?php

namespace BackendBundle\Controller;

use BackendBundle\Entity\Dictionary;
use BackendBundle\Form\DictionaryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Dictionary controller.
 *
 * @Route("/dictionary")
 */
class DictionaryController extends Controller
{
    /**
     * Lists all Dictionary entities.
     *
     * @Route("/", name="admin_dictionary_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dictionaries = $em->getRepository('BackendBundle:Dictionary')->findAll();

        return $this->render(
            'dictionary/index.html.twig',
            array(
                'dictionaries' => $dictionaries,
            )
        );
    }

    /**
     * Creates a new Dictionary entity.
     *
     * @Route("/new", name="admin_dictionary_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $dictionary = new Dictionary();
        $form = $this->createForm('BackendBundle\Form\DictionaryType', $dictionary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dictionary);
            $em->flush();

            return $this->redirectToRoute(
                'admin_dictionary_show',
                array('id' => $dictionary->getId())
            );
        }

        return $this->render(
            'dictionary/new.html.twig',
            array(
                'dictionary' => $dictionary,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Dictionary entity.
     *
     * @Route("/{id}", name="admin_dictionary_show")
     * @Method("GET")
     */
    public function showAction(Dictionary $dictionary)
    {
        $deleteForm = $this->createDeleteForm($dictionary);

        return $this->render(
            'dictionary/show.html.twig',
            array(
                'dictionary' => $dictionary,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Dictionary entity.
     *
     * @Route("/{id}/edit", name="admin_dictionary_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Dictionary $dictionary)
    {
        $deleteForm = $this->createDeleteForm($dictionary);
        $editForm = $this->createForm('BackendBundle\Form\DictionaryType', $dictionary);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dictionary);
            $em->flush();

            return $this->redirectToRoute(
                'admin_dictionary_edit',
                array('id' => $dictionary->getId())
            );
        }

        return $this->render(
            'dictionary/edit.html.twig',
            array(
                'dictionary' => $dictionary,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Deletes a Dictionary entity.
     *
     * @Route("/{id}", name="admin_dictionary_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Dictionary $dictionary)
    {
        $form = $this->createDeleteForm($dictionary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dictionary);
            $em->flush();
        }

        return $this->redirectToRoute('admin_dictionary_index');
    }

    /**
     * Creates a form to delete a Dictionary entity.
     *
     * @param Dictionary $dictionary The Dictionary entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Dictionary $dictionary)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('admin_dictionary_delete', array('id' => $dictionary->getId()))
            )
            ->setMethod('DELETE')
            ->getForm();
    }
}
