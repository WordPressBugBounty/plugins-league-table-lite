const {registerBlockType} = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const {Component} = wp.element;
const { __ } = wp.i18n;
const { SelectControl, PanelBody, PanelRow } = wp.components;

class BlockEdit extends Component {

  constructor(props) {

    super(...arguments);
    this.props = props;

    //Initialize the attributes
    if(typeof this.props.attributes.tableId === 'undefined'){
      this.props.setAttributes({tableId: '0'});
    }

  }

  render() {

    return [
      <div key="daextletal-image" className="daextletal-block-image">{__('Table', 'daextletal')}</div>,
      <InspectorControls key="inspector">
        <PanelBody title={__('Table', 'league-table-lite')}
                   initialOpen={true}>
          <PanelRow className="panel-row-table">
            <SelectControl
                label="Table"
                value={this.props.attributes.tableId }
                options={window.DAEXTLETAL_PARAMETERS.tables}
                onChange={(newTableId) => this.props.setAttributes({ tableId: newTableId })}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
    ];

  }

}

/**
 * Register the Gutenberg block
 */
registerBlockType('daextletal/table', {
  title: __('League Table', 'league-table-lite'),
  category: 'design',
  keywords: [
    __('table', 'league-table-lite'),
    __('league', 'league-table-lite'),
    __('grid', 'league-table-lite'),
  ],
  attributes: {
    tableId: {
      type: 'string',
    },
  },

  /**
   * The edit function describes the structure of your block in the context of the editor.
   * This represents what the editor will render when the block is used.
   *
   * The "edit" property must be a valid function.
   *
   * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
   */
  edit: BlockEdit,

  /**
   * The save function defines the way in which the different attributes should be combined
   * into the final markup, which is then serialized by Gutenberg into post_content.
   *
   * The "save" property must be specified and must be a valid function.
   *
   * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
   */
  save: function() {

    /**
     * This is a dynamic block and the rendering is performed with PHP:
     *
     * https://wordpress.org/gutenberg/handbook/blocks/creating-dynamic-blocks/
     */
    return null;

  },

});
