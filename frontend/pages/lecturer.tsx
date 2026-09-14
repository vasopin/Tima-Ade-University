import { GetServerSideProps } from 'next'

// This page has been removed. Returning 404 so /lecturer is no longer accessible.
export const getServerSideProps: GetServerSideProps = async () => ({
  notFound: true,
})

export default function LecturerRemoved() {
  return null
}
