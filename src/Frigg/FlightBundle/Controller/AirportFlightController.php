<?php

namespace Frigg\FlightBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Frigg\FlightBundle\Entity\Flight;
use Frigg\FlightBundle\Entity\Airport;
//use Frigg\FlightBundle\Form\FlightType;

class AirportFlightController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Collection get action
     * @var Request $request
     * @var integer $airportId Id of the entity's airport
     * @return array
     *
     * @Rest\View()
     */
    public function cgetAction(Request $request, $airportId)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$airport = $this->getAirport($airportId)) {
            throw $this->createNotFoundException('Unable to find airport entity');
        }

        $flights = $em->getRepository('FriggFlightBundle:Flight')->findBy(
            array(
                'airport' => $airport->getId(),
            )
        );

        return array(
            'entity' => $airport,
            'children' => $flights
        );
    }

    /**
     * Get action
     * @var integer $airportId Id of the entity's airport
     * @var integer $id Id of the entity
     * @return array
     *
     * @Rest\View()
     */
    public function getAction($airportId, $id)
    {
        $entity = $this->getEntity($airportId, $id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Collection post action
     * @var Request $request
     * @var integer $airportId Id of the entity's airport
     * @return View|array
     */
    public function cpostAction(Request $request, $airportId)
    {
        /*$airport = $this->getAirport($airportId);
        $entity = new Flight();
        $entity->setAirport($airport);
        $form = $this->createForm(new FlightType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_airport_flight',
                    array(
                        'airportId' => $entity->getAirport()->getId(),
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
     * @var integer $airportId Id of the entity's airport
     * @var integer $id Id of the entity
     * @return View|array
     */
    public function putAction(Request $request, $airportId, $id)
    {
        /*$entity = $this->getEntity($airportId, $id);
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
     * @var integer $airportId Id of the entity's airport
     * @var integer $id Id of the entity
     * @return View
     */
    public function deleteAction($airportId, $id)
    {
        $entity = $this->getEntity($airportId, $id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Get entity instance
     * @var integer $airportId Id of the entity's airport
     * @var integer $id Id of the entity
     * @return Flight
     */
    protected function getEntity($airportId, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$airport = $this->getAirport($airportId)) {
            return $this->createNotFoundException('Unable to find airport entity');
        }

        $entity = $em->getRepository('FriggFlightBundle:Flight')->findOneBy(
            array(
                'code' => $id,
                'airport' => $airport->getId(),
            )
        );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find flight entity');
        }

        return $entity;
    }

    /**
     * Get airport instance
     * @var integer $code Code of the airport
     * @return Airport
     */
    protected function getAirport($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FriggFlightBundle:Airport')->findOneByCode($code);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find airport entity');
        }

        return $entity;
    }
}