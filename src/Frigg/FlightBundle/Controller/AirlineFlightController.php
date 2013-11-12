<?php

namespace Frigg\FlightBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\Airline;
//use Frigg\FlightBundle\Form\FlightType;

class AirlineFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Collection get action
     * @var Request $request
     * @var integer $airlineId Id of the entity's airline
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request, $airlineId)
    {
        $em = $this->getDoctrine()->getManager();

        $airline = $this->getAirline($airlineId);
        $flights = $em->getRepository('FriggFlightBundle:Flight')->findBy(
            array(
                'airline' => $airline->getId(),
            )
        );

        return array(
            'entity' => $airline,
            'children' => $flights
        );
    }

    /**
     * Get action
     * @var integer $airlineId Id of the entity's airline
     * @var integer $id Id of the entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airlineId, $id)
    {
        $entity = $this->getEntity($airlineId, $id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Collection post action
     * @var Request $request
     * @var integer $airlineId Id of the entity's airline
     * @return View|array
     */
    public function cpostAction(Request $request, $airlineId)
    {
        /*$airline = $this->getAirline($airlineId);
        $entity = new Flight();
        $entity->setAirline($airline);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airline_flight',
                    array(
                        'airlineId' => $entity->getAirline()->getId(),
                        'id' => $entity->getId()
                    )
                ),
                Codes::HTTP_CREATED
            );
        }

        return array(
            'form' => $form,
        );*/
        return array(
            'form' => false
        );
    }

    /**
     * Put action
     * @var Request $request
     * @var integer $airlineId Id of the entity's airline
     * @var integer $id Id of the entity
     * @return View|array
     */
    public function putAction(Request $request, $airlineId, $id)
    {
        /*$entity = $this->getEntity($airlineId, $id);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        return array(
            'form' => $form,
        );*/
        return array(
            'form' => false
        );
    }

    /**
     * Delete action
     * @var integer $airlineId Id of the entity's airline
     * @var integer $id Id of the entity
     * @return View
     */
    public function deleteAction($airlineId, $id)
    {
        $entity = $this->getEntity($airlineId, $id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Get entity instance
     * @var integer $airlineId Id of the entity's airline
     * @var integer $id Id of the entity
     * @return Flight
     */
    protected function getEntity($airlineId, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $airline = $this->getAirline($airlineId);
        $entity = $em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'code' => $id,
                'airline' => $airline->getId(),
            )
        );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find flight entity');
        }

        return $entity;
    }

    /**
     * Get airline instance
     * @var integer $code Code of the airline
     * @return Airline
     */
    protected function getAirline($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FriggFlightBundle:Airline')->findOneByCode($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find airline entity');
        }

        return $entity;
    }
}
