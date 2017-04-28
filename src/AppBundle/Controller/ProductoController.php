<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Producto;
use AppBundle\Form\ProductoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductoController extends Controller
{
   /**
    * @Route (path="/", name="app_producto_lista")
    */

   public function IndexAction()
   {
       $m = $this->getDoctrine()->getManager();
       $repo = $m->getRepository('AppBundle:Producto');

       $m->flush();
       $producto = $repo->findAll();
       return $this->render(':producto:lista.html.twig',
           [
               'productos' => $producto
           ]);
   }

    /**
     * @Route (path="/añadir",
     * name="app_producto_añadir")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function añadirAction()
    {
        $Producto = new Producto();
        $form = $this->createForm(ProductoType::class, $Producto);

        return $this->render(':producto:form.html.twig',
            [
                'form'  => $form->createView(),
                'action'  => $this->generateUrl('app_producto_añadirlo'),
            ]);
    }

    /**
     * @Route (path="/añadirlo",
     *      name="app_producto_añadirlo")
     * @Security("has_role('ROLE_USER')")
     */

    public function añadirloAction(Request $request)
    {

        $Producto= new Producto();
        $form = $this->createForm(ProductoType::class, $Producto);

        $form->handleRequest($request);

        if($form->isValid()) {
            $user = $this->getUser();
            $Producto->setAutor($user);
            $m = $this->getDoctrine()->getManager();
            $m->persist($Producto);
            $m->flush();

            return $this->redirectToRoute('app_producto_lista');
        }
            return $this->render(':producto:form.html.twig',
                 [
                     'form'  => $form->createView(),
                     'action'  => $this->generateUrl('app_producto_añadirlo')
                 ]);

    }

    /**
     * @Route (
     *     path="/actualizar/{id}",
     *     name="app_producto_actualizar"
     * )
     * @Security("has_role('ROLE_USER')")
     */

    public function actualizarAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Producto');

        $Producto = $repo->find($id);

        $form = $this->createForm(ProductoType::class, $Producto);
        $user = $this->getUser();
        if($Producto->getAutor() == $user || $user == 'admin') {
        return $this->render(':producto:form.html.twig',
            [

                'form' => $form->CreateView(),
                'action' => $this->generateUrl('app_producto_actualizarlo', ['id' => $id]),
            ]);
        } else {
                    return $this->redirectToRoute('app_producto_lista');
                }

    }

    /**
     * @Route (
     *     path="/actualizarlo/{id}",
     *     name="app_producto_actualizarlo")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */

    public function actualizarloAction($id, Request $request)
    {

        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Producto');
        $Producto = $repo->find($id);
        $form = $this->createForm(ProductoType::class, $Producto);
        $user = $this->getUser();


            $form->handleRequest($request);
            if ($form->isValid()) {
                $m->flush();

                return $this->redirectToRoute('app_producto_lista');
            }

        return $this->render(':producto:form.html.twig',
            [
                'form' => $form->CreateView(),
                'action' => $this->generateUrl('app_producto_actualizarlo', ['id' => $id]),
            ]);

    }

    /**
     * @Route (
     *     path="/eliminar/{id}",
     *     name="app_producto_eliminar"
     * )
     * @Security("has_role('ROLE_USER')")
     */

    public function eliminarAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Producto');

        $Producto = $repo->find($id);
        if($Producto->getAutor() == $this->getUser() || $this->getUser() == 'admin' ) {
            $m->remove($Producto);
            $m->flush();

            $this->addFlash('messages', 'Producto eliminado');

            return $this->redirectToRoute('app_producto_lista');
        }
        else{
           return $this->redirectToRoute('app_producto_lista');
        }
    }

}
