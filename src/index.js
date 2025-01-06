const { registerBlockType } = wp.blocks;
const { RichText, MediaUpload, BlockControls } = wp.editor;
const { Button } = wp.components;

// Enregistrer un nouveau bloc personnalisé
registerBlockType( 'photo-wall/gutenberg-block', {
    title: 'Mon Bloc Image',
    description: 'Un bloc personnalisé qui permet de télécharger une image.',
    category: 'common',
    icon: 'format-image',
    attributes: {
        imageURL: {
            type: 'string',
            default: ''
        },
        imageAlt: {
            type: 'string',
            default: ''
        }
    },
    edit: ( props ) => {
        const { attributes, setAttributes } = props;
        const { imageURL, imageAlt } = attributes;

        return (
            <div className="mon-plugin-gutenberg-block">
                <BlockControls>
                    <MediaUpload
                        onSelect={ ( media ) => setAttributes( { imageURL: media.url, imageAlt: media.alt } ) }
                        allowedTypes={ [ 'image' ] }
                        value={ imageURL }
                        render={ ( { open } ) => (
                            <Button onClick={ open }>Choisir une image</Button>
                        ) }
                    />
                </BlockControls>

                { imageURL && (
                    <img src={ imageURL } alt={ imageAlt } style={{ width: '100%' }} />
                )}

                <RichText
                    tagName="p"
                    placeholder="Ajouter une description..."
                    value={ imageAlt }
                    onChange={ ( value ) => setAttributes( { imageAlt: value } ) }
                />
            </div>
        );
    },
    save: ( props ) => {
        const { attributes } = props;
        const { imageURL, imageAlt } = attributes;

        return (
            <div className="mon-plugin-gutenberg-block">
                { imageURL && (
                    <img src={ imageURL } alt={ imageAlt } style={{ width: '100%' }} />
                )}
                <RichText.Content tagName="p" value={ imageAlt } />
            </div>
        );
    }
} );
