<?php
/**
 * Created by PhpStorm.
 * User: Aevyn
 * Date: 19/01/2021
 * Time: 10:13
 */

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(RegionRepository $regionRepository, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        return $this->render('contact.html.twig', [
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

}