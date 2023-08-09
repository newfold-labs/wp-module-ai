import { useState, useEffect, createPortal } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import DescriptionGenerator from '@newfold-labs/wp-module-ai';

function ExcerptCustomPanel() {
  const [targetElement, setTargetElement] = useState(null);
  // const isSidebarOpened = useSelect((select) => select('core/edit-post').isEditorSidebarOpened(), []);

  // const element = document.querySelector('#editor .editor-post-excerpt');

  useEffect(() => {
    const checkForElement = async () => {
      while (!document.querySelector('#editor .editor-post-excerpt')) {
        await new Promise(resolve => setTimeout(resolve, 300)); // Wait for 300ms
      }
      setTargetElement(document.querySelector('#editor .editor-post-excerpt'));
    };
  
    const editorElement = document.querySelector('#editor');
    if (editorElement) {
      // Initialize the MutationObserver
      const observer = new MutationObserver(checkForElement);
  
      // Start observing changes in the editor's child elements
      observer.observe(editorElement, { childList: true, subtree: true });
  
      // Run the check initially
      checkForElement();
  
      // Clean up the observer when the component unmounts
      return () => observer.disconnect();
    }
  }, []);

  if (!targetElement) {
    console.log("nahi mila");
    return null;
  }
  return <PluginDocumentSettingPanel
      name="excerpt-custom-panel"
      title="Excerpt Custom Panel"
      className="excerpt-custom-panel"
    >
      {createPortal(
        <DescriptionGenerator
        siteDesc={"This is a driving school"}
        siteTitle={" Driving school site"}
        siteSubtype=""
        siteType=""
        siteUrl={"google.com"}
        targetElementSelector="#editor .editor-post-excerpt .editor-post-excerpt__textarea textarea"
      />, targetElement
      )}
    </PluginDocumentSettingPanel>
}

registerPlugin('excerpt-custom-panel', {
  render: ExcerptCustomPanel,
});

export default ExcerptCustomPanel;
