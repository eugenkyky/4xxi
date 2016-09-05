<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="stock")
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50))
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=10))
     */
    protected $symbol;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true})
     */
    protected $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Portfolio", inversedBy="stocks")
     * @ORM\JoinColumn(name="stock_id", referencedColumnName="id")
     */
    protected $portfolio;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Stock
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set symbol.
     *
     * @param string $symbol
     *
     * @return Stock
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Get symbol.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set amount.
     *
     * @param int $amount
     *
     * @return Stock
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set portfolio.
     *
     * @param Portfolio $portfolio
     *
     * @return Stock
     */
    public function setPortfolio($portfolio)
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    /**
     * Set portfolio.
     *
     * @return Portfolio
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }
}
