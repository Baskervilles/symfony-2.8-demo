<?php

/**
 * (c) 2016 Marcin Jasiński.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */
namespace ApiRestBundle\Controller;

use BackendBundle\Entity\Dictionary;
use BackendBundle\Form\DictionaryType;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class DictionaryController.
 *
 * @author Marcin Jasiński <mkjasinski@gmail.com>
 */
class DictionaryController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     description="Returns a collection of dictionary",
     *     output="BackendBundle\Entity\Dictionary[]",
     *     method="GET"
     * )
     *
     * @Get("")
     * @Get("/")
     *
     * @return Dictionary[]
     */
    public function dictionariesAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository("BackendBundle:Dictionary");

        $dictionaries = $repository->findAll();

        return $dictionaries;
    }

    /**
     * @ApiDoc(
     *     description="Returns dictionary",
     *     output="BackendBundle\Entity\Dictionary",
     *     method="GET",
     *     requirements={
     *          {
     *              "name"="dictionaryId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="dictionary identifier"
     *          }
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when dictionary is not found"
     *     }
     * )
     *
     * @Get("/{dictionaryId}", requirements={"dictionaryId"="\d+"})
     *
     * @param $dictionaryId
     *
     * @return Dictionary|View
     */
    public function dictionaryDetailsAction($dictionaryId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository("BackendBundle:Dictionary");

        $dictionary = $repository->findOneBy(array('id' => (int) $dictionaryId));

        if ($dictionary == null) {
            return $this->view(null, Codes::HTTP_NOT_FOUND);
        }

        return $dictionary;
    }

    /**
     * @ApiDoc(
     *     description="Deletes dictionary",
     *     method="DELETE",
     *     requirements={
     *          {
     *              "name"="dictionaryId",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="dictionary identifier"
     *          }
     *     },
     *     statusCodes={
     *          204="Returned when successful",
     *          404="Returned when dictionary is not found"
     *     }
     * )
     *
     * @Delete("/{dictionaryId}", requirements={"dictionaryId"="\d+"})
     *
     * @param $dictionaryId
     *
     * @return View
     */
    public function dictionaryDeleteAction($dictionaryId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository("BackendBundle:Dictionary");

        $dictionary = $repository->findOneBy(array('id' => (int) $dictionaryId));

        if ($dictionary == null) {
            return $this->view(null, Codes::HTTP_NOT_FOUND);
        }

        $entityManager->remove($dictionary);

        $entityManager->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * @ApiDoc(
     *     description="Creates dictionary",
     *     method="POST",
     *     statusCodes={
     *          201="Returned when successful",
     *          400="Returned when dictionary is not valid"
     *     },
     *     input="BackendBundle\Form\DictionaryType"
     * )
     *
     * @Post("")
     * @Post("/")
     *
     * @param Request $request
     *
     * @return View
     */
    public function dictionaryCreateAction(Request $request)
    {
        $dictionary = new Dictionary();

        $form = $this->get("form.factory")->createNamed(
            '',
            new DictionaryType(),
            $dictionary,
            array('csrf_protection' => false)
        );
        $form->submit($request);

        if ($form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($dictionary);

            $entityManager->flush();

            return $this->view(
                null,
                Codes::HTTP_CREATED,
                array(
                    'Location' => $this->generateUrl(
                        'api_dictionary_details',
                        array('dictionaryId' => $dictionary->getId()),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                )
            );
        }

        return $this->view($form, Codes::HTTP_BAD_REQUEST);
    }
}
