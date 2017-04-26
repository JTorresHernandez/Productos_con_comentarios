<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Producto;
use AppBundle\Entity\Comentario;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\ComentarioType;
use Symfony\Component\HttpFoundation\Request;


class ComentarioController extends Controller
{


    /**
     *
     *@Route("/nuevoC/{id}", name="app_comentario_crearAction")
     *
     */
        public function nuevoCAction(Request $req, Producto $id) {


            $m = $this->getDoctrine()->getManager();
            $r = $m->getRepository('AppBundle:Producto');
            $producto = $r->find($id);


            $comentario =  new Comentario();

            $comentario->setMessage($req->request->get('messageInput'));
            $comentario->setCreator($this->getUser());
            $comentario->setProducto($producto);
            $m->persist($comentario);
            $m->flush();


            return $this->redirectToRoute('app_producto_lista');
        }


    /**
     * @Route("/eliminarC/{id}", name="app_comentario_eliminar")
     *
     *
     */

        public function eliminarCAction(Comentario $comentario) {

            $Producto = $comentario->getProducto();
            if ($this->getUser() == $comentario->getCreator() or $this->getUser() == 'torres' or $this->getUser() == $Producto->getAutor()){
                $m = $this->getDoctrine()->getManager();
                $m->remove($comentario);
                $m->flush();
                return $this->redirectToRoute('app_producto_lista');
            } else {
                return $this->redirectToRoute('app_producto_lista');
            }
        }




}
