<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CartType;
use App\Repository\CartProductRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\RegionRepository;
use ContainerOKlTGpL\getCartProductRepositoryService;
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
        $entityManager = $this->getDoctrine()->getManager();
        $current_cart = $this->getUser()->getLastCart();
        $cartproducts = $current_cart->getCartProducts();
        foreach ($cartproducts as $cartproduct) {
            $product = $cartproduct->getProduct();
            $product->setQuantity(($product->getQuantity()) - ($cartproduct->getQuantity()));
            $entityManager->persist($product);
            $entityManager->flush();
        }
        $cart = new Cart();
        $this->getUser()->addCart($cart);
        $entityManager->persist($cart);
        $entityManager->persist($this->getUser());
        $entityManager->flush();

        $this->addFlash('success', 'Your order has been validated !');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/{id}/show", name="cart_show", methods={"GET"})
     * @IsGranted ("ROLE_USER")
     * @param Cart $cart
     * @param CategoryRepository $categoryRepository
     * @param RegionRepository $regionRepository
     * @return Response
     */
    public function show(Cart $cart, CategoryRepository $categoryRepository, RegionRepository $regionRepository): Response
    {
        if ($this->checkUser($cart->getUser())) {
            return $this->render('cart/show.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'regions' => $regionRepository->findAll(),
                'cart' => $cart,
            ]);
        }
        return $this->redirectToRoute('cart_show', ['id' => $this->getUser()->getLastCart()->getId()]);
    }

    /**
     * @Route("/{id}/add/{product}/{remove}", name="add_to_cart", methods={"GET"})
     * @IsGranted ("ROLE_USER")
     * @param Cart $cart
     * @param CartProductRepository $cartProductRepository
     * @param Product|null $product
     * @return Response
     */
    public
    function addProduct(Cart $cart, CartProductRepository $cartProductRepository, Product $product, String $remove = null): Response
    {
        if ($this->checkUser($cart->getUser())) {
            $cartProduct = $cartProductRepository->findOneBy(['cart' => $cart->getId(), 'product' => $product->getId()]);
            if ($cartProduct && $remove) {
                if ($cartProduct->getQuantity() > 0) {
                    $cartProduct->setQuantity($cartProduct->getQuantity() - 1);
                } else {
                    $this->addFlash('danger', 'There is no more of that product in your cart to remove');
                    return $this->redirectToRoute('cart_show', ['id' => $cart->getId()]);
                }
            } elseif ($cartProduct) {
                if ($product->getQuantity() > $cartProduct->getQuantity()) {
                    $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
                } else {
                    $this->addFlash("danger", 'Not enough stock');
                    return $this->redirectToRoute('cart_show', ['id' => $cart->getId()]);
                }

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
        return $this->redirectToRoute('cart_show', ['id' => $this->getUser()->getLastCart()->getId()]);

    }

    /**
     * @Route("/{id}/empty", name="empty_cart", methods={"GET", "POST"})
     * @IsGranted ("ROLE_USER")
     * @param Cart $cart
     * @param CartProductRepository $cartProductRepository
     * @return Response
     */
    public
    function emptyCart(Cart $cart, CartProductRepository $cartProductRepository): Response
    {
        if ($this->checkUser($cart->getUser())) {
            $cartProducts = $cartProductRepository->findBy(['cart' => $cart->getId()]);
            foreach ($cartProducts as $cartProduct) {

                $cart->removeCartProduct($cartProduct);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('cart_show', ['id' => $cart->getId()]);
        }
        return $this->redirectToRoute('cart_show', ['id' => $this->getUser()->getLastCart()->getId()]);
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
    public
    function delete(Request $request, Cart $cart, CategoryRepository $categoryRepository, RegionRepository $regionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cart->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cart_index');
    }

    public
    function checkUser(User $user): bool
    {
        if ($user == $this->getUser()) {
            return true;
        }
        return false;
    }
}
