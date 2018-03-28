<?php declare(strict_types=1);

namespace Shopware\Storefront\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Shopware\Cart\Exception\LineItemNotFoundException;
use Shopware\Cart\LineItem\LineItem;
use Shopware\CartBridge\Product\ProductProcessor;
use Shopware\CartBridge\Service\StoreFrontCartService;
use Shopware\CartBridge\Voucher\VoucherProcessor;
use Shopware\Context\Struct\StorefrontContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends StorefrontController
{
    /**
     * Route name to cart index action
     */
    public const ROUTE_CHECKOUT_CART = 'checkout_cart';

    /**
     * Route name to checkout confirm action
     */
    public const ROUTE_CHECKOUT_CONFIRM = 'checkout_confirm';

    /**
     * ONLY WORK IN PROGRESS
     */
    public const USER_LOGGED_IN = true;

    /**
     * @Route("/cart", name="cart_index", options={"seo"="false"})
     */
    public function indexAction()
    {
        return $this->redirectToRoute('checkout_cart');
    }

    /**
     * @Route("/cart/addProduct", name="cart_add_product", options={"seo"="false"})
     * @Method({"POST"})
     */
    public function addProductAction(Request $request, StorefrontContext $context)
    {
        $identifier = $request->request->get('identifier');
        $quantity = $request->request->getInt('quantity');
        $target = $request->request->get('target');
        $services = $request->request->get('service');

        if (!($identifier && $quantity)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid identifier or quantity',
            ]);
        }

        $key = $identifier;
        if (!empty($services)) {
            $services = array_values($services);
            $key = $identifier . '-' . implode('-', $services);
        }

        /** @var StoreFrontCartService $cartService */
        $cartService = $this->get(StoreFrontCartService::class);
        $cartService->add(
            new LineItem(
                $key,
                ProductProcessor::TYPE_PRODUCT,
                $quantity,
                ['id' => $identifier, 'services' => $services]
            ),
            $context
        );

        return $this->conditionalResponse($request, $target);
    }

    /**
     * @Route("/cart/removeLineItem", name="cart_delete_line_item", options={"seo"="false"})
     * @Method({"POST"})
     */
    public function removeLineItemAction(Request $request, StorefrontContext $context)
    {
        $identifier = $request->request->get('identifier');
        $target = $request->request->get('target');

        if (!$identifier) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid identifier',
            ]);
        }

        $cartService = $this->get(StoreFrontCartService::class);
        $cartService->remove($identifier, $context);

        return $this->conditionalResponse($request, $target);
    }

    /**
     * @Route("/cart/setLineItemQuantity", name="cart_set_line_item_quantity", options={"seo"="false"})
     * @Method({"POST"})
     */
    public function setLineItemQuantityAction(Request $request, StorefrontContext $context)
    {
        $identifier = $request->request->get('identifier');
        $quantity = $request->request->getInt('quantity');
        $target = $request->request->get('target');

        if (!($identifier && $quantity)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid identifier or quantity',
            ]);
        }

        $cartService = $this->get(StoreFrontCartService::class);
        try {
            $cartService->changeQuantity($identifier, $quantity, $context);
        } catch (LineItemNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'LineItem not found',
            ]);
        }

        return $this->conditionalResponse($request, $target);
    }

    /**
     * @Route("/cart/addVoucher", name="cart_add_voucher", options={"seo"="false"})
     * @Method({"POST"})
     */
    public function addVoucherAction(Request $request, StorefrontContext $context)
    {
        $identifier = $request->request->get('identifier', false);
        $target = $request->request->get('target');

        if (!$identifier) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid identifier',
            ]);
        }

        $cartService = $this->get(StoreFrontCartService::class);
        $cartService->add(
            new LineItem($identifier, VoucherProcessor::TYPE_VOUCHER, 1, ['code' => $identifier]),
            $context
        );

        return $this->conditionalResponse($request, $target);
    }

    /**
     * Serve response depending on target and current user state
     *
     * @param Request $request
     * @param string  $target
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    private function conditionalResponse(Request $request, ?string $target): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true]);
        }

        if ($target == self::ROUTE_CHECKOUT_CONFIRM) {
            return $this->redirectToRoute(self::ROUTE_CHECKOUT_CONFIRM);
        }

        return $this->redirectToRoute(self::ROUTE_CHECKOUT_CART);
    }
}