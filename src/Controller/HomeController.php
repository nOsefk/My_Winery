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

class HomeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(RegionRepository $regionRepository, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        return $this->render('home/index.html.twig', [
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'products' => $productRepository->findBy(array(),array('id'=>'DESC'),2,0)
        ]);
    }

}