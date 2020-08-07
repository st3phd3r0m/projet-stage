<?php
namespace App\Twig;

use App\Repository\LanguagesRepository;
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
        ];
    }

    protected $languagesRepository;


    public function __construct(LanguagesRepository $languagesRepository)
    {
        $this->languagesRepository = $languagesRepository;
    }

    public function showForeignLanguages()
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->neq('name', 'fr'));
        return $this->languagesRepository->matching($criteria); 
    }
}