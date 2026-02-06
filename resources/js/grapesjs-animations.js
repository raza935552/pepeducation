/**
 * Animation Builder for GrapesJS.
 * Adds scroll/load/hover animation traits to any component.
 * Runtime uses IntersectionObserver for scroll-triggered animations.
 */
export default function registerAnimations(editor) {
    const animTypes = [
        { value: '', name: 'None' },
        { value: 'fade-in', name: 'Fade In' },
        { value: 'slide-up', name: 'Slide Up' },
        { value: 'slide-down', name: 'Slide Down' },
        { value: 'slide-left', name: 'Slide Left' },
        { value: 'slide-right', name: 'Slide Right' },
        { value: 'zoom-in', name: 'Zoom In' },
        { value: 'zoom-out', name: 'Zoom Out' },
        { value: 'flip-x', name: 'Flip Horizontal' },
        { value: 'bounce', name: 'Bounce' },
    ];

    const triggers = [
        { value: 'scroll', name: 'On Scroll (default)' },
        { value: 'load', name: 'On Page Load' },
        { value: 'hover', name: 'On Hover' },
    ];

    // Add animation traits when a component is selected
    editor.on('component:selected', (component) => {
        const tm = editor.TraitManager;
        const existing = component.get('traits')?.models || [];
        if (existing.some(t => t.get('name') === 'data-anim')) return;

        component.addTrait([
            { type: 'select', name: 'data-anim', label: 'Animation', options: animTypes },
            { type: 'select', name: 'data-anim-trigger', label: 'Trigger', options: triggers },
            { type: 'text', name: 'data-anim-duration', label: 'Duration (ms)', placeholder: '600' },
            { type: 'text', name: 'data-anim-delay', label: 'Delay (ms)', placeholder: '0' },
        ]);
    });
}
