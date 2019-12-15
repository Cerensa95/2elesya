<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/setting")
 */
class SettingController extends Controller
{
    /**
     * @Route("/", name="setting_index", methods="GET")
     */
    public function index(SettingRepository $settingRepository): Response
    {
        $setdata = $settingRepository->findAll();

        if(!$setdata) {
            $setting = new Setting();
            $em = $this->getDoctrine()->getManager();
            $setting->setTitle("Site");

            $em->persist($setting);
            $em->flush();
            $setdata = $settingRepository->findAll();
        }
        return $this->redirectToRoute('setting_edit',['id'=>$setdata[0]->getId()]);


    }

    /**
     * @Route("/new", name="setting_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $setting = new Setting();
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            return $this->redirectToRoute('setting_index');
        }

        return $this->render('setting/new.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="setting_show", methods="GET")
     */
    public function show(Setting $setting): Response
    {
        return $this->render('setting/show.html.twig', ['setting' => $setting]);
    }

    /**
     * @Route("/{id}/edit", name="setting_edit", methods="GET|POST")
     */
    public function edit(Request $request, Setting $setting): Response
    {
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        //dump($request);
          //  die();


        if ($form->isSubmitted() && $form->isValid()) {

            

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('setting_edit', ['id' => $setting->getId()]);
        }

        return $this->render('admin/setting/edit.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="setting_delete", methods="DELETE")
     */
    public function delete(Request $request, Setting $setting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$setting->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($setting);
            $em->flush();
        }

        return $this->redirectToRoute('setting_index');
    }

}
