<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Portfolio;
use AppBundle\Form\StockType;
use AppBundle\Entity\Stock;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class StockController extends Controller
{
    /**
     * Creates stock entity.
     *
     * @Route("/portfolio/{portfolio}/stock_new", name="create_stock")
     * @Method({"GET", "POST"})
     *
     * @param Request   $request   users Requets
     * @param Portfolio $portfolio Portfolio Entity
     *
     * @throws \Exception if YQL return bad response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, Portfolio $portfolio)
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $this->get('guzzle.client.api_crm');
            $response = $client->get('https://query.yahooapis.com/v1/public/yql?'.
                'q=select%20*%20from%20yahoo.finance.quote%20where%20symbol%20in%20(%22'.$stock->getSymbol().'%22)&'.
                'format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=');

            if (200 === $response->getStatusCode()) {
                $response = json_decode($response->getBody(), true);

                if (null == $response['query']['results']['quote']['Name']) {
                    $error = new FormError('Can not find stock witn symbol: '.$stock->getSymbol());
                    $form->addError($error);
                } else {
                    $stock->setName($response['query']['results']['quote']['Name']);
                    $stock->setSymbol($response['query']['results']['quote']['Symbol']);
                    $stock->setPortfolio($portfolio);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();

                    return $this->redirectToRoute('stock_success', array('stock' => $stock->getId()));
                }
            } else {
                throw new \Exception('Something went wrong!');
            }
        }

        return $this->render('stock/add_stock.html.twig', array(
            'form' => $form->createView(),
            'portfolio' => $portfolio,
        ));
    }

    /**
     * Shows stock Entity.
     *
     * @Route("/stock/{stock}", name="show_stock", requirements={"stock": "\d+"})
     *
     * @param Stock $stock The Stock entity
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Stock $stock)
    {
        return $this->render('stock/show_stock.html.twig', array(
            'stock' => $stock,
        ));
    }

    /**
     * Shows success page.
     *
     * @Route("/stock/{stock}/success", name="stock_success", requirements={"stock": "\d+"})
     *
     * @param Stock $stock The Stock entity
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addStockSuccess(Stock $stock)
    {
        return $this->render('stock/stock_success.html.twig', array(
            'stock' => $stock,
        ));
    }

    /**
     * Displays a form to edit an existing Stock entity.
     *
     * @Route("/stock/{stock}/edit", name="stock_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request users HTTP request
     * @param Stock   $stock   The Stock entity
     *
     * @throws \Exception if YQL return bad response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Stock $stock)
    {
        $editForm = $this->createForm('AppBundle\Form\StockType', $stock);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $client = $this->get('guzzle.client.api_crm');
            $response = $client->get('https://query.yahooapis.com/v1/public/yql?'.
                'q=select%20*%20from%20yahoo.finance.quote%20where%20symbol%20in%20(%22'.$stock->getSymbol().'%22)&'.
                'format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=');

            if (200 === $response->getStatusCode()) {
                $response = json_decode($response->getBody(), true);

                if (null == $response['query']['results']['quote']['Name']) {
                    $error = new FormError('Can not find stock witn symbol: '.$stock->getSymbol());
                    $editForm->addError($error);
                } else {
                    $stock->setName($response['query']['results']['quote']['Name']);
                    $stock->setSymbol($response['query']['results']['quote']['Symbol']);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($stock);
                    $em->flush();

                    return $this->redirectToRoute('stock_success', array('stock' => $stock->getId()));
                }
            } else {
                throw new \Exception('Something went wrong!');
            }
        }

        return $this->render('stock/edit_stock.html.twig', array(
            'stock' => $stock,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Stock entity.
     *
     * @Route("/stock/{stock}/delete", name="stock_delete")
     * @Method({"GET", "DELETE"})
     *
     * @param Request $request users HTTP request
     * @param Stock   $stock   The Stock entity
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Stock $stock)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('stock_delete', array('stock' => $stock->getId())))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stock);
            $em->flush();

            return $this->redirectToRoute('show_portfolio', array('portfolio' => $stock->getPortfolio()->getId()));
        }

        return $this->render('stock/delete_stock.html.twig', array(
            'delete_form' => $form->createView(),
        ));
    }
}
