<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Form\CartType;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\RegionRepository;
use phpDocumentor\Reflection\Types\Boolean;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{

    /**
     * @Route("/new", name="cart_new", methods={"GET","POST"})
     * @IsGranted ("ROLE_USER")
     */
    public function new(Request $request, CategoryRepository $categoryRepository, RegionRepository $regionRepository): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('cart/new.html.twig', [
            'cart' => $cart,
            'categories' => $categoryRepository->findAll(),
            'regions' => $regionRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cart_show", methods={"GET"})
     * @Route("/{id}/add/{product}/{remove}", name="add_to_cart", methods={"GET"})
     * @IsGranted ("ROLE_USER")
     * @param Cart $cart
     * @param CategoryRepository $categoryRepository
     * @param RegionRepository $regionRepository
     * @param CartProductRepository $cartProductRepository
     * @param Product|null $product
     * @return Response
     */
    public function show(Cart $cart, CategoryRepository $categoryRepository, RegionRepository $regionRepository, CartProductRepository $cartProductRepository, Product $product = null, String $remove = null): Response
    {

        if ($product) {
            $cartProduct = $cartProductRepository->findOneBy(['cart' => $cart->getId(), 'product' => $product->getId()]);
            if ($cartProduct && $remove) {
                $cartProduct->setQuantity($cartProduct->getQuantity() - 1);
            } elseif ($cartProduct) {
                $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
            } else {
                $cartProduct = new CartProduct();
                $cartProduct->setCart($cart);
                $cartProduct->setProduct($product);
                $cartProduct->setQuantity(1);
            }
            $cart->addCartProduct($cartProduct);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cartProduct);
            $entityManager->persist($cart);
            $entityManager->flush();
            return $this->redirectToRoute('cart_show', ['id' => $cart->getId()]);
        }


        return $this->render('cart/show.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'regions' => $regionRepository->findAll(),
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/{id}/empty", name="empty_cart", methods={"GET", "POST"})
     * @IsGranted ("ROLE_USER")
     * @param Cart $cart
     * @param CartProductRepository $cartProductRepository
     * @return Response
     */
    public function emptyCart(Cart $cart, CartProductRepository $cartProductRepository): Response
    {

        $cartProducts = $cartProductRepository->findBy(['cart' => $cart->getId()]);
        foreach ($cartProducts as $cartProduct) {

            $cart->removeCartProduct($cartProduct);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->redirectToRoute('cart_show', ['id' => $cart->getId()]);
    }




    /*
    /*
        /**
         * @Route("/{id}/edit", name="cart_edit", methods={"GET","POST"})
         * @IsGranted ("ROLE_USER")

   public function edit(Request $request, Cart $cart, CategoryRepository $categoryRepository, RegionRepository $regionRepository): Response
    {
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('cart/edit.html.twig', [
            'cart' => $cart,
            'categories' => $categoryRepository->findAll(),
            'regions' => $regionRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
*/
    /**
     * @Route("/{id}", name="cart_delete", methods={"DELETE"})
     * @IsGranted ("ROLE_USER")
     */
    public function delete(Request $request, Cart $cart, CategoryRepository $categoryRepository, RegionRepository $regionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cart->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index');
    }
}
