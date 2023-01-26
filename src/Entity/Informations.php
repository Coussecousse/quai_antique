<?php

namespace App\Entity;

use DateTime;

class Informations 
{
    private string $name;
    private int $person;
    private array $allergies = [];
    private ?string $allergiesOther = null;
    private ?\DateTimeInterface $date = null;
    

    /**
     * Get the value of name
     */ 
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of person
     */ 
    public function getPerson() : int
    {
        return $this->person;
    }

    /**
     * Set the value of person
     *
     * @return  self
     */ 
    public function setPerson($person) : self
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get the value of allergies
     */ 
    public function getAllergies() : array
    {
        return $this->allergies;
    }

    /**
     * Set the value of allergies
     *
     * @return  self
     */ 
    public function setAllergies($allergies) : self
    {
        $this->allergies = $allergies;

        return $this;
    }

    /**
     * Get the value of allergies_other
     */ 
    public function getAllergiesOther() : string
    {
        return $this->allergiesOther;
    }

    /**
     * Set the value of allergies_other
     *
     * @return  self
     */ 
    public function setAllergiesOther($allergiesOther) : self
    {
        $this->allergiesOther = $allergiesOther;

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate() :DateTime
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date) :self
    {
        $this->date = $date;

        return $this;
    }
}