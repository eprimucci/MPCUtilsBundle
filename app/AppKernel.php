<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            
            // my bundle
            new CodigoAustral\MPCUtilsBundle\CodigoAustralMPCUtilsBundle(),
            
            // doctrine migrations and fixtures
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            
            // Users from a trusted source
            new FOS\UserBundle\FOSUserBundle(),
            
            // Bootstrap, Sass, KnpMenus and paginator
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Mopa\Bundle\BootstrapSandboxBundle\MopaBootstrapSandboxBundle(),
            new Liip\ThemeBundle\LiipThemeBundle(),
            
            // QRcode
            new Endroid\Bundle\QrCodeBundle\EndroidQrCodeBundle(),
            
            // JMS Di (dependency injection extras)
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            
            // JMS Security extra-bundles (good for all JMS)
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            
            // jquery routing
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            
            // PDFs
            new Siphoc\PdfBundle\SiphocPdfBundle(),
            
            
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
