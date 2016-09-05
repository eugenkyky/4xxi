<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Portfolio;
use AppBundle\Form\PortfolioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PortfolioController extends Controller
{
    /**
     * Lists all Portfolio entities.
     *
     * @Route("/", name="list_portfolios")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $portfolios = $this->getDoctrine()->getRepository('AppBundle:Portfolio')->findAll();

        return $this->render('portfolio/list_portfolios.html.twig', array(
            'portfolios' => $portfolios,
        ));
    }

    /**
     * Show portfolio entity.
     *
     * @Route("/portfolio/{portfolio}", name="show_portfolio", requirements={"portfolio": "\d+"})
     * @Method("GET")
     *
     * @param Portfolio $portfolio The Portfolio entity
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Portfolio $portfolio)
    {
        return $this->render('portfolio/show_portfolio.html.twig', array(
            'portfolio' => $portfolio,
        ));
    }

    /**
     * Creates Portfolio Entity.
     *
     * @Route("/portfolio/create", name="create_portfolio")
     * @Method({"GET", "POST"})
     *
     * @param Request $request users HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $portfolio = new Portfolio();

        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();

            return $this->redirectToRoute('list_portfolios');
        }

        return $this->render('portfolio/add_portfolio.html.twig', array(
            'form' => $form->createView(),
            'portfolio' => $portfolio,
        ));
    }

    /**
     * Shows portfolios finance history.
     *
     * @Route("/portfolio/{portfolio}/history", name="show_portfolio_history")
     * @Method("GET")
     *
     * @param Request   $request   users HTTP request
     * @param Portfolio $portfolio The Portfolio entity
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showHistoryAction(Request $request, Portfolio $portfolio)
    {
        $stocks = $portfolio->getStocks();
        $resultArray = array();

        $logger = $this->get('logger');
        if (count($stocks) > 0) {
            foreach ($stocks as $stock) {
                for ($i = 0; $i < 2; ++$i) {
                    //Окно в один год
                    $startDate = (new \DateTime('now'))->modify('-'.($i + 1).' years');
                    $endDate = (new \DateTime('now'))->modify('-'.$i.' years');
                    //Убираем пересечение дат
                    if (1 == $i) {
                        $endDate = $endDate->modify('-1 day');
                    }

                    $client = $this->get('guzzle.client.api_crm');
                    $response = $client->get('https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.'.
                        'historicaldata%20where%20symbol%20=%20"'.$stock->getSymbol().'"%20and%20startDate%20%3D%20%22'.
                        $startDate->format('Y-m-d').
                        '%22%20and%20endDate%20%3D%20%22'.
                        $endDate->format('Y-m-d').'%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=');

                    if (200 === $response->getStatusCode()) {
                        $response = json_decode($response->getBody(), true);
                        foreach ($response['query']['results']['quote'] as $quote) {
                            $logger->info($quote['Date']);
                            $logger->info($quote['Close']);

                            if (empty($resultArray[$quote['Date']])) {
                                $resultArray[$quote['Date']] = ($quote['Close'] * $stock->getAmount());
                            } else {
                                $resultArray[$quote['Date']] += ($quote['Close'] * $stock->getAmount());
                            }
                        }
                    }
                }
            }
        }

        return $this->render('portfolio/show_portfolio_history.html.twig', array(
            'resultArray' => $resultArray,
            'portfolio' => $portfolio,
        ));
    }

    /**
     * Displays a form to edit an existing Portfolio entity.
     *
     * @Route("/portflio/{portfolio}/edit", name="portfolio_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request   $request   users HTTP request
     * @param Portfolio $portfolio The Portfolio entity
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Portfolio $portfolio)
    {
        $editForm = $this->createForm('AppBundle\Form\PortfolioType', $portfolio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();

            return $this->redirectToRoute('show_portfolio', array('portfolio' => $portfolio->getId()));
        }

        return $this->render('portfolio/edit_portfolio.html.twig', array(
            'portfolio' => $portfolio,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Portfolio entity.
     *
     * @Route("/portfolio/{portfolio}/delete", name="portfolio_delete")
     * @Method({"GET","DELETE"})
     *
     * @param Request   $request   users HTTP request
     * @param Portfolio $portfolio The Portfolio entity
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Portfolio $portfolio)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', array('portfolio' => $portfolio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($portfolio);
            $em->flush();

            return $this->redirectToRoute('list_portfolios');
        }

        return $this->render('portfolio/delete_portfolio.html.twig', array(
            'delete_form' => $form->createView(),
        ));
    }
}
