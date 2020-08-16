<?php
namespace App\Twig;

use App\Repository\LanguagesRepository;
use App\Repository\LinksRepository;
use Doctrine\Common\Collections\Criteria;
use Twig\Extension\AbstractExtension;

use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('foreignLanguages', [$this, 'showForeignLanguages']),
            new TwigFunction('defaultLanguage', [$this, 'showDefaultLanguage']),
            new TwigFunction('links', [$this, 'showLinks']),
        ];
    }

    protected $languagesRepository;
    protected $linksRepository;

    public function __construct(LanguagesRepository $languagesRepository, LinksRepository $linksRepository)
    {
        $this->languagesRepository = $languagesRepository;
        $this->linksRepository = $linksRepository;
    }

    public function showForeignLanguages()
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->neq('name', 'fr'));
        return $this->languagesRepository->matching($criteria); 
    }

    public function showLinks()
    {
        return $this->linksRepository->findAll();
    }
}