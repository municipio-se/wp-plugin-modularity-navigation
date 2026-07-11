<?php

declare(strict_types=1);

namespace MunicipioModularityNavigation\Customizer;

use Municipio\Customizer\KirkiField;
use Municipio\Customizer\KirkiPanelSection;

final class GridSettings
{
    public const PARENT_PANEL_ID = 'municipio_customizer_panel_design_component';
    public const SECTION_ID = 'municipio_customizer_section_mod_navigation';
    public const GRID_STYLE_SETTING = 'mod_navigation_grid_style';

    /**
     * Add Navigation to Municipios existing component-appearance panel after the panel
     * exists. The legacy setting name is intentional: imported LTS theme mods then keep
     * their explicit value and need no migration.
     */
    public function register(object $panel): void
    {
        if (!method_exists($panel, 'getID') || $panel->getID() !== self::PARENT_PANEL_ID) {
            return;
        }

        KirkiPanelSection::create()
            ->setID(self::SECTION_ID)
            ->setPanel(self::PARENT_PANEL_ID)
            ->setTitle(esc_html__('Navigation', 'modularity-navigation'))
            ->setActiveCallback(static fn(): bool => post_type_exists('mod-navigation'))
            ->setFieldsCallback([$this, 'registerFields'])
            ->register();
    }

    public function registerFields(): void
    {
        KirkiField::addField([
            'type' => 'select',
            'settings' => self::GRID_STYLE_SETTING,
            'label' => esc_html__('Style for grid format', 'modularity-navigation'),
            'section' => self::SECTION_ID,
            'default' => 'blocks',
            'priority' => 10,
            'choices' => [
                'blocks' => esc_html__('Blocks', 'modularity-navigation'),
            ],
        ]);
    }
}
