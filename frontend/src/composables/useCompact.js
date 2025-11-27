import { ref, onMounted, onBeforeUnmount } from 'vue'

/**
 * useCompact - Shared composable for compact mode behavior
 * @param {number} breakpoint - Viewport width below which compact mode activates (default: 1024)
 * @returns {object} - { compactMobile: Ref<boolean> }
 */
export function useCompact(breakpoint = 1024) {
  const compactMobile = ref(false)

  const updateCompact = () => {
    if (typeof window === 'undefined') return
    compactMobile.value = window.innerWidth < breakpoint
  }

  onMounted(() => {
    updateCompact()
    window.addEventListener('resize', updateCompact)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', updateCompact)
  })

  return { compactMobile }
}

export default useCompact
