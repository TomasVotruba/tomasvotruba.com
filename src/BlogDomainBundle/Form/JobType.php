<?php

/*
 * This file is part of Symfonisti.cz.
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('website', 'url', [
            'label' => 'Odkaz na inzerát'
        ]);
        $builder->add('submit', 'submit', [
            'label' => 'Přidat'
        ]);
    }
}
